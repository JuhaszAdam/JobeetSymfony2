<?php

namespace MyBundle\Manager;

use Doctrine\Entity;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use MyBundle\CacheDriver\AbstractCacheDriver;

class Manager implements ManagerInterface
{
    /**
     * @var AbstractCacheDriver
     */
    private $cacheDriver;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * @var int
     */
    private $flushInterval = 5;

    /**
     * @var String
     */
    private $entityClassPath;

    /**
     * @var String
     */
    private $entityClassName;

    /**
     * @param EntityManager $entityManager
     * @param AbstractCacheDriver $cacheDriver
     * @param String $classPath
     * @param String $repositoryPath
     */
    public function __construct(EntityManager $entityManager, AbstractCacheDriver $cacheDriver, $classPath, $repositoryPath)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository($repositoryPath);
        $this->entityClassPath = $classPath;
        $this->cacheDriver = $cacheDriver;
       // $this->entityClassName = array_pop(explode("\\", $classPath));
    }

    /**
     * @return int
     */
    public function getFlushInterval()
    {
        return $this->flushInterval;
    }

    /**
     * @param int $flushInterval
     */
    public function setFlushInterval($flushInterval)
    {
        $this->flushInterval = $flushInterval;
    }

    /**
     * {@inheritdoc}
     */
    public function pushToDatabase(array $saveList )
    {
        $i = 0;
        $this->entityManager->getConnection()->beginTransaction();
        try {
            foreach ($saveList as $entityToSave) {
                $this->save($entityToSave);

                if (++$i % $this->flushInterval == 0) {
                    $this->entityManager->flush();
                    $this->entityManager->getConnection()->commit();
                }
            }
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();

            $this->toCacheMultiple($saveList);
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        }
    }

    private function save($entityToSave)
    {
        $this->entityManager->getConnection()->beginTransaction();

        try {
            $this->entityManager->persist($entityToSave);
            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        }
    }

    /**
     * @param array $saveList
     * @param int $ttl
     * @throws \Exception
     */
    public function toCacheMultiple($saveList, $ttl = 600)
    {
        try {
            foreach ($saveList as $entityToSave) {
                $this->toCache($entityToSave, $ttl);
            }
        } catch (\Exception $e) {
            throw new \Exception("Exception thrown by the Cache Driver ( " . get_class($this->cacheDriver) . " )"
                . PHP_EOL . "Message: " . $e->getMessage() . PHP_EOL);
        }
    }

    /**
     * @param Entity $saveEntity
     * @param int $ttl
     * @throws \Exception
     */
    public function toCache($saveEntity, $ttl = 600)
    {
        try {
            /**@var Entity $saveEntity */
            $this->cacheDriver->set($this->generateKey($this->entityClassName, $saveEntity->getId()), serialize($saveEntity), $ttl);
        } catch (\Exception $e) {
            throw new \Exception("Exception thrown by the Cache Driver ( " . get_class($this->cacheDriver) . " )"
                . PHP_EOL . "Message: " . $e->getMessage() . PHP_EOL);
        }
    }

    /**
     * @param int $id
     * @return null|Entity
     * @throws \Exception
     */
    public function fromCache($id)
    {
        $result = null;
        try {
            $result = $this->cacheDriver->get($this->generateKey($this->entityClassName, $id));
        } catch (\Exception $e) {
            throw new \Exception("Exception thrown by the Cache Driver ( " . get_class($this->cacheDriver) . " )"
                . PHP_EOL . "Message: " . $e->getMessage() . PHP_EOL);
        }

        return unserialize($result);
    }

    /**
     * {@inheritdoc}
     */
    public function findFromDatabase()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy($criteria, array $orderBy = null, $limit = null, $offset = null)
    {

        if (is_int($criteria) && $this->cacheDriver->exists($criteria)) {
            $result = $this->cacheDriver->get($criteria);
        } else {
            $result = $this->repository->findBy($criteria, $orderBy, $limit, $offset);

            if (is_array($result)) {
                $this->toCacheMultiple($result);
            } else {
                /** @var Entity $result */
                $this->toCache($result);
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return new $this->entityClassPath;
    }

    /**
     * @param string $entityId
     * @param string $className
     * @return string
     */
    private function generateKey($className, $entityId)
    {
        return ($className . '/' . $entityId);
    }
}
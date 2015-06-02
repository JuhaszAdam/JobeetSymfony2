<?php

namespace ShepardBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use ShepardBundle\Utils;
use ShepardBundle\Utils\Jobeet;

use FOS\ElasticaBundle\Configuration\Search;
use Zend_Search_Lucene_Field;

/**
 * @Search(repositoryClass="ShepardBundle\Repository\JobRepository")
 */
class Job
{
    /**
     * @var
     */
    public $file;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $company;

    /**
     * @var string
     */
    private $logo;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $position;

    /**
     * @var string
     */
    private $location;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $how_to_apply;

    /**
     * @var string
     */
    private $token;

    /**
     * @var boolean
     */
    private $is_public;

    /**
     * @var boolean
     */
    private $is_activated;

    /**
     * @var string
     */
    private $email;

    /**
     * @var DateTime
     */
    private $expires_at;

    /**
     * @var DateTime
     */
    private $created_at;

    /**
     * @var DateTime
     */
    private $updated_at;

    /**
     * @var Category
     */
    private $category;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $type
     * @return Job
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $company
     * @return Job
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $logo
     * @return Job
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param string $url
     * @return Job
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $position
     * @return Job
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $location
     * @return Job
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $description
     * @return Job
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $howToApply
     * @return Job
     */
    public function setHowToApply($howToApply)
    {
        $this->how_to_apply = $howToApply;

        return $this;
    }

    /**
     * @return string
     */
    public function getHowToApply()
    {
        return $this->how_to_apply;
    }

    /**
     * @param string $token
     * @return Job
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param boolean $isPublic
     * @return Job
     */
    public function setIsPublic($isPublic)
    {
        $this->is_public = $isPublic;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsPublic()
    {
        return $this->is_public;
    }

    /**
     * @param boolean $isActivated
     * @return Job
     */
    public function setIsActivated($isActivated)
    {
        $this->is_activated = $isActivated;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsActivated()
    {
        return $this->is_activated;
    }

    /**
     * @param string $email
     * @return Job
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param DateTime $expiresAt
     * @return Job
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expires_at = $expiresAt;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getExpiresAt()
    {
        return $this->expires_at;
    }

    /**
     * @param DateTime $createdAt
     * @return Job
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param DateTime $updatedAt
     * @return Job
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param Category $category
     * @return Job
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {

    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {

    }

    public function getCompanySlug()
    {
        return Jobeet::slugify($this->getCompany());
    }

    public function getPositionSlug()
    {
        return Jobeet::slugify($this->getPosition());
    }

    public function getLocationSlug()
    {
        return Jobeet::slugify($this->getLocation());
    }

    /**
     * @ORM\PrePersist
     */
    public function setExpiresAtValue()
    {
        if (!$this->getExpiresAt()) {
            $now = $this->getCreatedAt() ? $this->getCreatedAt()->format('U') : time();
            $this->expires_at = new DateTime(date('Y-m-d H:i:s', $now + 86400 * 30));
        }
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return ['full-time' => 'Full time', 'part-time' => 'Part time', 'freelance' => 'Freelance'];
    }

    /**
     * @return array
     */
    public static function getTypeValues()
    {
        return array_keys(self::getTypes());
    }

    /**
     * @return string
     */
    protected function getUploadDir()
    {
        return 'uploads/jobs';
    }

    /**
     * @return string
     */
    protected function getUploadRootDir()
    {
        return __DIR__ . '/../../../../web/' . $this->getUploadDir();
    }

    /**
     * @return null|string
     */
    public function getWebPath()
    {
        return null === $this->logo ? null : $this->getUploadDir() . '/' . $this->logo;
    }

    /**
     * @return null|string
     */
    public function getAbsolutePath()
    {
        return null === $this->logo ? null : $this->getUploadRootDir() . '/' . $this->logo;
    }

    /**
     * @ORM\PrePersist
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            $this->created_at = new DateTime(date('Y-m-d H:i:s'));

            $this->logo = uniqid() . '.' . $this->file->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        $this->file->move($this->getUploadRootDir(), $this->logo);

        unset($this->file);
    }

    /**
     * @ORM\PostRemove
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }

    public function setTokenValue()
    {
        if (!$this->getToken()) {
            $this->token = sha1($this->getEmail() . rand(11111, 99999));
        }
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        return $this->getDaysBeforeExpires() < 0;
    }

    /**
     * @return bool
     */
    public function expiresSoon()
    {
        return $this->getDaysBeforeExpires() < 5;
    }

    /**
     * @return float
     */
    public function getDaysBeforeExpires()
    {
        return ceil(($this->getExpiresAt()->format('U') - time()) / 86400);
    }

    public function publish()
    {
        $this->setIsActivated(true);
    }

    /**
     * @return bool
     */
    public function extend()
    {
        if (!$this->expiresSoon()) {
            return false;
        }

        $this->expires_at = new DateTime(date('Y-m-d H:i:s', time() + 86400 * 30));

        return true;
    }

    /**
     * @param $host
     * @return array
     */
    public function asArray($host)
    {
        return [
            'category' => $this->getCategory()->getName(),
            'type' => $this->getType(),
            'company' => $this->getCompany(),
            'logo' => $this->getLogo() ? 'http://' . $host . '/uploads/jobs/' . $this->getLogo() : null,
            'url' => $this->getUrl(),
            'position' => $this->getPosition(),
            'location' => $this->getLocation(),
            'description' => $this->getDescription(),
            'how_to_apply' => $this->getHowToApply(),
            'expires_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @return mixed
     */
    public static function getLuceneIndex()
    {
        if (file_exists($index = self::getLuceneIndexFile())) {
            return \Zend_Search_Lucene::open($index);
        }

        return \Zend_Search_Lucene::create($index);
    }

    /**
     * @return string
     */
    public static function getLuceneIndexFile()
    {
        return __DIR__ . '/../../../../web/data/job.index';
    }

    /**
     * @ORM\PostPersist
     */
    public function updateLuceneIndex()
    {
        $index = self::getLuceneIndex();

        $this->deleteLuceneIndex();

        if ($this->isExpired() || !$this->getIsActivated()) {
            return;
        }

        $doc = new \Zend_Search_Lucene_Document();

        $doc->addField(Zend_Search_Lucene_Field::Keyword('pk', $this->getId()));

        $doc->addField(Zend_Search_Lucene_Field::UnStored('position', $this->getPosition(), 'utf-8'));
        $doc->addField(Zend_Search_Lucene_Field::UnStored('company', $this->getCompany(), 'utf-8'));
        $doc->addField(Zend_Search_Lucene_Field::UnStored('location', $this->getLocation(), 'utf-8'));
        $doc->addField(Zend_Search_Lucene_Field::UnStored('description', $this->getDescription(), 'utf-8'));

        $index->addDocument($doc);
        $index->commit();
    }

    public function deleteLuceneIndex()
    {
        $index = self::getLuceneIndex();

        foreach ($index->find('pk:' . $this->getId()) as $hit) {
            $index->delete($hit->id);
        }
    }
}

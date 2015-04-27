<?php

namespace MyBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use MyBundle\Entity\Affiliate;
use MyBundle\Entity\CategoryAffiliate;

class LoadAffiliatesData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $em)
    {
        $affiliate = new Affiliate();
        $affiliate->setUrl("http://www.sensio-labs.com/");
        $affiliate->setEmail("fabien.potencier@example.com");
        $affiliate->setToken('sensio-labs');
        $affiliate->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));
        $affiliate->setIsActive(true);

        $categoryAffiliateProgramming = new CategoryAffiliate();
        $categoryAffiliateProgramming->setCategory($em->merge($this->getReference('category-programming')));
        $categoryAffiliateProgramming->setAffiliate($affiliate);


        $affiliate->addCategoryAffiliate($categoryAffiliateProgramming);

        $em->persist($affiliate);

        $affiliate = new Affiliate();
        $affiliate->setUrl("/");
        $affiliate->setEmail("fabien2.potencier@example.com");
        $affiliate->setToken('symfony');
        $affiliate->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));
        $affiliate->setIsActive(false);
        $categoryAffiliateDesign = new CategoryAffiliate();
        $categoryAffiliateDesign->setCategory($em->merge($this->getReference('category-design')));
        $categoryAffiliateDesign->setAffiliate($affiliate);
        $categoryAffiliateProgramming->setAffiliate($affiliate);

        $affiliate->addCategoryAffiliate($categoryAffiliateProgramming);
        $affiliate->addCategoryAffiliate($categoryAffiliateDesign);

        $em->persist($categoryAffiliateProgramming);
        $em->persist($categoryAffiliateDesign);

        $em->persist($affiliate);

        $em->flush();

        $this->addReference('affiliate', $affiliate);
    }

    public function getOrder()
    {
        return 3;
    }
}

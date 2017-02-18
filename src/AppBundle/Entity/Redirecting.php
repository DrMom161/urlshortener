<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Redirecting
 *
 * @ORM\Table(name="redirecting")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RedirectingRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Redirecting
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="longUrl", type="string", length=1000)
     */
    private $longUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="shortUrl", type="string", length=10, unique=true)
     */
    private $shortUrl;

    /**
     * @var int
     *
     * @ORM\Column(name="usage_count", type="integer")
     */
    private $usageCount = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * Add created at date
     * @ORM\PrePersist
     */
    public function onPrePersistSetRegistrationDate()
    {
        $this->createdAt = new \DateTime();
    }
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set longUrl
     *
     * @param string $longUrl
     *
     * @return Redirecting
     */
    public function setLongUrl($longUrl)
    {
        $this->longUrl = $longUrl;

        return $this;
    }

    /**
     * Get longUrl
     *
     * @return string
     */
    public function getLongUrl()
    {
        return $this->longUrl;
    }

    /**
     * Set shortUrl
     *
     * @param string $shortUrl
     *
     * @return Redirecting
     */
    public function setShortUrl($shortUrl)
    {
        $this->shortUrl = $shortUrl;

        return $this;
    }

    /**
     * Get shortUrl
     *
     * @return string
     */
    public function getShortUrl()
    {
        return $this->shortUrl;
    }

    /**
     * Set usageCount
     *
     * @param integer $usageCount
     *
     * @return Redirecting
     */
    public function setUsageCount($usageCount)
    {
        $this->usageCount = $usageCount;

        return $this;
    }

    /**
     * Get usageCount
     *
     * @return int
     */
    public function getUsageCount()
    {
        return $this->usageCount;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Redirecting
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}

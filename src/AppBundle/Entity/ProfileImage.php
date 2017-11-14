<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;

/**
 * ProfileImage
 *
 * @ORM\Table(name="profile_image")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProfileImageRepository")
 */
class ProfileImage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Exclude
     * @ORM\Column(name="cdn", type="string", length=255, nullable=true)
     */
    private $cdn;

    /**
     * @var string
     * @Exclude
     * @ORM\Column(name="folder", type="string", length=255, nullable=true)
     */
    private $folder;

    /**
     * @var string
     * @Exclude
     * @ORM\Column(name="extension", type="string", length=10, nullable=true)
     */
    private $extension;

    /**
     * @var string
     * @Exclude
     * @ORM\Column(name="name", type="string", length=1024, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="full_url", type="string", length=1024, nullable=true)
     */
    private $fullUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="cdn_full_url", type="string", length=1024, nullable=true)
     */
    private $cdnFullUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="original_full_url", type="string", length=1024, nullable=true)
     */
    private $originalFullUrl;

    /**
     * @var array<string>
     *
     * @ORM\Column(name="thumb_urls", type="array", options={"comment":"Array of All Thumb URLs"})
     */
    private $thumbsUrls;


    /******************** */
    /**** Relations ***** */
    /******************** */

    /**
     * @Exclude
     * @ORM\ManyToOne(targetEntity="User", inversedBy="profileImages")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     * */
    private $user;

    /****************************/
    /****** END Relations *******/
    /****************************/

    /***********************/
    /*** Some Basics ****/
    /***********************/

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /*** END Basics ****/

    public function __construct() {
        $this->setCreationTime(new \DateTime());
        $this->status = 1;
        $this->active = 1;
    }

    public function __toString() {
        return $this->name;
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
     * Set cdn
     *
     * @param string $cdn
     *
     * @return ProfileImage
     */
    public function setCdn($cdn)
    {
        $this->cdn = $cdn;

        return $this;
    }

    /**
     * Get cdn
     *
     * @return string
     */
    public function getCdn()
    {
        return $this->cdn;
    }

    /**
     * Set folder
     *
     * @param string $folder
     *
     * @return ProfileImage
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder
     *
     * @return string
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Set extension
     *
     * @param string $extension
     *
     * @return ProfileImage
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ProfileImage
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set fullUrl
     *
     * @param string $fullUrl
     *
     * @return ProfileImage
     */
    public function setFullUrl($fullUrl)
    {
        $this->fullUrl = $fullUrl;

        return $this;
    }

    /**
     * Get fullUrl
     *
     * @return string
     */
    public function getFullUrl()
    {
        return $this->fullUrl;
    }

    /**
     * Set cdnFullUrl
     *
     * @param string $cdnFullUrl
     *
     * @return ProfileImage
     */
    public function setCdnFullUrl($cdnFullUrl)
    {
        $this->cdnFullUrl = $cdnFullUrl;

        return $this;
    }

    /**
     * Get cdnFullUrl
     *
     * @return string
     */
    public function getCdnFullUrl()
    {
        return $this->cdnFullUrl;
    }

    /**
     * Set originalFullUrl
     *
     * @param string $originalFullUrl
     *
     * @return ProfileImage
     */
    public function setOriginalFullUrl($originalFullUrl)
    {
        $this->originalFullUrl = $originalFullUrl;

        return $this;
    }

    /**
     * Get originalFullUrl
     *
     * @return string
     */
    public function getOriginalFullUrl()
    {
        return $this->originalFullUrl;
    }

    /**
     * Set creationTime
     *
     * @param \DateTime $creationTime
     *
     * @return ProfileImage
     */
    public function setCreationTime($creationTime)
    {
        $this->creationTime = $creationTime;

        return $this;
    }

    /**
     * Get creationTime
     *
     * @return \DateTime
     */
    public function getCreationTime()
    {
        return $this->creationTime;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return ProfileImage
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return ProfileImage
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return ProfileImage
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set thumbsUrls
     *
     * @param array $thumbsUrls
     *
     * @return ProfileImage
     */
    public function setThumbsUrls($thumbsUrls)
    {
        $this->thumbsUrls = $thumbsUrls;

        return $this;
    }

    /**
     * Get thumbsUrls
     *
     * @return array
     */
    public function getThumbsUrls()
    {
        return $this->thumbsUrls;
    }
}

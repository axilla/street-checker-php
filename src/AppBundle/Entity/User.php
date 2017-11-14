<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"basic_info"})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="username", type="string", length=128, nullable=false)
     * @Groups({"basic_info"})
     */
    private $username;

    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var boolean
     * @ORM\Column(name="admin",type="boolean",nullable=false)
     */
    private $admin;

    /**
     * @var string
     * @Exclude
     * @ORM\Column(name="password", type="string", length=128, nullable=false)
     */
    private $password;

    /**
     * @var string
     * @Exclude
     * @ORM\Column(name="slat", type="string", length=128, nullable=false)
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @var string
     * @ORM\Column(name="gender", type="string", length=10, nullable=true)
     */
    protected $gender;

    /**
     * @var string
     * @Exclude
     * @ORM\Column(name="sc_access_token", type="string", length=32, nullable=true)
     */
    protected $scAccessToken;

    /**
     * @var \DateTime
     * @Exclude
     * @ORM\Column(name="is_loggedin", type="boolean", nullable=true)
     */
    private $isLoggedIn = TRUE;

    /**
     * @var \DateTime
     * @Exclude
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    private $lastLogin;

    /**
     * @var \DateTime
     * @Exclude
     * @ORM\Column(name="last_access", type="datetime", nullable=true)
     */
    private $lastAccess;

    /**
     * @var boolean
     * @Exclude
     * @ORM\Column(name="locked", type="boolean", nullable=true)
     */
    protected $locked;

    /**
     * @var \DateTime
     * @Exclude
     * @ORM\Column(name="last_locking", type="datetime", nullable=true)
     */
    private $lastLocking;

    /**
     * @var integer
     * @Exclude
     * @ORM\Column(name="locking_count", type="integer", nullable=true)
     */
    private $lockingCount;

    /**
     * @var string
     * @Exclude
     * @ORM\Column(name="forgot_pass_token", type="string", length=255, unique=true, nullable=true)
     */
    private $forgotPassToken;

    /**
     * @var \DateTime
     * @Exclude
     * @ORM\Column(name="last_FPT_generated", type="datetime", nullable=true)
     */
    private $lastFPTgenerated;

    /********************/
    /*** Some Basics ****/
    /********************/

    /**
     * @var \DateTime
     * @Exclude
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var \DateTime
     * @Exclude
     * @ORM\Column(name="last_update_time", type="datetime")
     */
    private $lastUpdateTime;

    /***** END Basics ****/

    /******************** */
    /**** Relations ***** */
    /******************** */


    /**
     * @Exclude
     * @ORM\OneToMany(targetEntity="Report", mappedBy="owner", orphanRemoval=true, cascade={"all"})
     * */
    private $reports;

    /**
     * @Exclude
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="owner", orphanRemoval=true, cascade={"all"})
     * */
    private $comments;

    /**
     * @Exclude
     * @ORM\OneToMany(targetEntity="Impression", mappedBy="owner", orphanRemoval=true, cascade={"all"})
     * */
    private $impressions;

    /**
     * @ORM\OneToMany(targetEntity="ProfileImage", mappedBy="user", orphanRemoval=true, cascade={"all"})
     * */
    private $profileImages;

    /**
     * @Exclude
     * @ORM\OneToMany(targetEntity="ReportGrade", mappedBy="owner", orphanRemoval=true, cascade={"all"})
     * */
    private $reportGrades;


    /************************ */
    /**** Relations END ***** */
    /************************ */


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setCreationTime(new \DateTime());
        $this->setLastUpdateTime(new \DateTime());
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->locked = 0;
        $this->admin = false;

        $this->forgotPassToken = NULL;
        $this->locked = 0;
        $this->lockingCount = 0;
        $this->lastLocking = NULL;
        $this->lastAccess = new \DateTime();
        $this->lastFPTgenerated = NULL;
        $this->lastLogin = new \DateTime();

        $this->reports = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->impressions = new ArrayCollection();
        $this->profileImages = new ArrayCollection();
        $this->reportGrades = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set creationTime
     *
     * @param \DateTime $creationTime
     *
     * @return User
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
     * Set lastUpdateTime
     *
     * @param \DateTime $lastUpdateTime
     *
     * @return User
     */
    public function setLastUpdateTime($lastUpdateTime)
    {
        $this->lastUpdateTime = $lastUpdateTime;

        return $this;
    }

    /**
     * Get lastUpdateTime
     *
     * @return \DateTime
     */
    public function getLastUpdateTime()
    {
        return $this->lastUpdateTime;
    }

    /**
     * Add Report
     *
     * @param \AppBundle\Entity\Report $report
     *
     * @return User
     */
    public function addReport(\AppBundle\Entity\Report $report)
    {
        $this->reports[] = $report;

        return $this;
    }

    /**
     * Remove Report
     *
     * @param \AppBundle\Entity\Report $Report
     */
    public function removeReport(\AppBundle\Entity\Report $report)
    {
        $this->reports->removeElement($report);
    }

    /**
     * Get Reports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReports()
    {
        return $this->reports->filter(function (Report $report) {
            return $report->getReport() == null;
        });
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResponses()
    {
        return $this->reports->filter(function (Report $report) {
            return $report->getReport() != null;
        });
    }

    /**
     * Add comment
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return User
     */
    public function addComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \AppBundle\Entity\Comment $comment
     */
    public function removeComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add impression
     *
     * @param \AppBundle\Entity\Impression $impression
     *
     * @return User
     */
    public function addImpression(\AppBundle\Entity\Impression $impression)
    {
        $this->impressions[] = $impression;

        return $this;
    }

    /**
     * Remove impression
     *
     * @param \AppBundle\Entity\Impression $impression
     */
    public function removeImpression(\AppBundle\Entity\Impression $impression)
    {
        $this->impressions->removeElement($impression);
    }

    /**
     * Get impressions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * Add profileImage
     *
     * @param \AppBundle\Entity\ProfileImage $profileImage
     *
     * @return User
     */
    public function addProfileImage(\AppBundle\Entity\ProfileImage $profileImage)
    {
        $this->profileImages[] = $profileImage;

        return $this;
    }

    /**
     * Remove profileImage
     *
     * @param \AppBundle\Entity\ProfileImage $profileImage
     */
    public function removeProfileImage(\AppBundle\Entity\ProfileImage $profileImage)
    {
        $this->profileImages->removeElement($profileImage);
    }

    /**
     * Get profileImages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProfileImages()
    {
        return $this->profileImages;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set scAccessToken
     *
     * @param string $scAccessToken
     *
     * @return User
     */
    public function setScAccessToken($scAccessToken)
    {
        $this->scAccessToken = $scAccessToken;

        return $this;
    }

    /**
     * Get scAccessToken
     *
     * @return string
     */
    public function getScAccessToken()
    {
        return $this->scAccessToken;
    }

    /**
     * Set isLoggedIn
     *
     * @param boolean $isLoggedIn
     *
     * @return User
     */
    public function setIsLoggedIn($isLoggedIn)
    {
        $this->isLoggedIn = $isLoggedIn;

        return $this;
    }

    /**
     * Get isLoggedIn
     *
     * @return boolean
     */
    public function getIsLoggedIn()
    {
        return $this->isLoggedIn;
    }

    /**
     * Set lastAccess
     *
     * @param \DateTime $lastAccess
     *
     * @return User
     */
    public function setLastAccess($lastAccess)
    {
        $this->lastAccess = $lastAccess;

        return $this;
    }

    /**
     * Get lastAccess
     *
     * @return \DateTime
     */
    public function getLastAccess()
    {
        return $this->lastAccess;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     *
     * @return User
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    public function lock()
    {
        $this->locked = 1;
        $this->setLastLocking(new \DateTime());

        return $this;
    }

    public function unlock()
    {
        $this->locked = 0;
        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set lastLocking
     *
     * @param \DateTime $lastLocking
     *
     * @return User
     */
    public function setLastLocking($lastLocking)
    {
        $this->lastLocking = $lastLocking;

        return $this;
    }

    /**
     * Get lastLocking
     *
     * @return \DateTime
     */
    public function getLastLocking()
    {
        return $this->lastLocking;
    }

    /**
     * Set lockingCount
     *
     * @param integer $lockingCount
     *
     * @return User
     */
    public function setLockingCount($lockingCount)
    {
        $this->lockingCount = $lockingCount;

        return $this;
    }

    /**
     * Get lockingCount
     *
     * @return integer
     */
    public function getLockingCount()
    {
        return $this->lockingCount;
    }

    /**
     * Set forgotPassToken
     *
     * @param string $forgotPassToken
     *
     * @return User
     */
    public function setForgotPassToken($forgotPassToken)
    {
        $this->forgotPassToken = $forgotPassToken;

        return $this;
    }

    /**
     * Get forgotPassToken
     *
     * @return string
     */
    public function getForgotPassToken()
    {
        return $this->forgotPassToken;
    }

    /**
     * Set lastFPTgenerated
     *
     * @param \DateTime $lastFPTgenerated
     *
     * @return User
     */
    public function setLastFPTgenerated($lastFPTgenerated)
    {
        $this->lastFPTgenerated = $lastFPTgenerated;

        return $this;
    }

    /**
     * Get lastFPTgenerated
     *
     * @return \DateTime
     */
    public function getLastFPTgenerated()
    {
        return $this->lastFPTgenerated;
    }

    /**
     * Loggin user
     *
     * @param \DateTime $lastLogin
     * @return User
     */
    public function login()
    {
        $this->isLoggedIn = 1;

        $now = new \DateTime();
        $this->setLastLogin($now);

        return $this;
    }

    /**
     * Loggout user
     *
     * @param \DateTime $lastLogin
     * @return User
     */
    public function logout()
    {
        $this->isLoggedIn = 0;

        return $this;
    }


    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     *
     * @return User
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $encoder = new MessageDigestPasswordEncoder('sha1');
        $password = $encoder->encodePassword($password, $this->getSalt());

        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->admin;
    }

    /**
     * @param bool $admin
     * @return $this
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
        return $this;
    }

    /**
     * @param mixed $reportGrades
     * @return User
     */
    public function setReportGrades($reportGrades)
    {
        $this->reportGrades = $reportGrades;
        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReportGrades()
    {
        return $this->reportGrades;
    }
}

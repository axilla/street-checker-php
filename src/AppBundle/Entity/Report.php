<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\Groups;


/**
 * Report
 *
 * @ORM\Table(name="report")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReportRepository")
 */
class Report
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
     * @ORM\Column(name="title", type="string", length=255)
     * @Groups({"basic_info"})
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1025, nullable=true)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="lat", type="float")
     */
    private $lat;

    /**
     * @var float
     *
     * @ORM\Column(name="lng", type="float")
     */
    private $lng;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=1025, nullable=true)
     */
    private $address;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="smallint", nullable=true)
     */
    private $level;

    /**
     * @var int
     *
     * @ORM\Column(name="urgency", type="smallint", nullable=true)
     */
    private $urgency;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=15, nullable=true)
     */
    private $type;

    /********************/
    /*** Some Basics ****/
    /********************/

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_time", type="datetime")
     */
    private $creationTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_update_time", type="datetime")
     */
    private $lastUpdateTime;


    /**
     * @var boolean
     *
     * @ORM\Column(name="active", options={"default": true})
     */
    private $active;

    /***** END Basics ****/

    /******************** */
    /**** Relations ***** */
    /******************** */

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="reports")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false)
     * */
    private $owner;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="report", orphanRemoval=true, cascade={"all"})
     * */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="Impression", mappedBy="report", orphanRemoval=true, cascade={"all"})
     * */
    private $impressions;

    /**
     * @ORM\OneToMany(targetEntity="ReportImage", mappedBy="report", orphanRemoval=true, cascade={"all"})
     * */
    private $reportImages;

    /**
     * @ORM\ManyToOne(targetEntity="ReportCategory", inversedBy="reports")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     * */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="Report", mappedBy="report")
     */
    private $responses;

    /**
     * @OneToOne(targetEntity="Report")
     * @JoinColumn(name="solution_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $solution;

    /**
     * Many Categories have One Category.
     * @ORM\ManyToOne(targetEntity="Report", inversedBy="responses")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id")
     */
    private $report;

    /**
     * @Exclude
     * @ORM\OneToMany(targetEntity="ReportGrade", mappedBy="report", orphanRemoval=true, cascade={"all"})
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
        $this->setActive(1);
        $this->setCreationTime(new \DateTime());
        $this->setLastUpdateTime(new \DateTime());
        $this->comments = new ArrayCollection();
        $this->impressions = new ArrayCollection();
        $this->reportImages = new ArrayCollection();
        $this->reportGrades = new ArrayCollection();
    }

    public function __toString()
    {
        return (string)$this->getTitle();
    }

    public function toArray()
    {
        return array(
            'id'    => $this->getId(),
            'title' => $this->getTitle()
        );
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
     * Set title
     *
     * @param string $title
     *
     * @return Report
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Report
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set lat
     *
     * @param float $lat
     *
     * @return Report
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param float $lng
     *
     * @return Report
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Report
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set level
     *
     * @param integer $level
     *
     * @return Report
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set urgency
     *
     * @param integer $urgency
     *
     * @return Report
     */
    public function setUrgency($urgency)
    {
        $this->urgency = $urgency;

        return $this;
    }

    /**
     * Get urgency
     *
     * @return int
     */
    public function getUrgency()
    {
        return $this->urgency;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Report
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set creationTime
     *
     * @param \DateTime $creationTime
     *
     * @return Report
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
     * @return Report
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
     * Set active
     *
     * @param string $active
     *
     * @return Report
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return string
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set owner
     *
     * @param \AppBundle\Entity\User $owner
     *
     * @return Report
     */
    public function setOwner(\AppBundle\Entity\User $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \AppBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Add comment
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return Report
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
     * @return Report
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
     * Set category
     *
     * @param \AppBundle\Entity\ReportCategory $category
     *
     * @return Report
     */
    public function setCategory(\AppBundle\Entity\ReportCategory $category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\ReportCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add reportImage
     *
     * @param \AppBundle\Entity\ReportImage $reportImage
     *
     * @return Report
     */
    public function addReportImage(\AppBundle\Entity\ReportImage $reportImage)
    {
        $this->reportImages[] = $reportImage;

        return $this;
    }

    /**
     * Remove reportImage
     *
     * @param \AppBundle\Entity\ReportImage $reportImage
     */
    public function removeReportImage(\AppBundle\Entity\ReportImage $reportImage)
    {
        $this->reportImages->removeElement($reportImage);
    }

    /**
     * Get reportImages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReportImages()
    {
        return $this->reportImages;
    }

    /**
     * Add response
     *
     * @param \AppBundle\Entity\Report $response
     *
     * @return Report
     */
    public function addResponse(\AppBundle\Entity\Report $response)
    {
        $this->responses[] = $response;

        return $this;
    }

    /**
     * Remove response
     *
     * @param \AppBundle\Entity\Report $response
     */
    public function removeResponse(\AppBundle\Entity\Report $response)
    {
        $this->responses->removeElement($response);
    }

    /**
     * Get responses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * Set report
     *
     * @param \AppBundle\Entity\Report $report
     *
     * @return Report
     */
    public function setReport(\AppBundle\Entity\Report $report = null)
    {
        $this->report = $report;
        $this->setLng($report->getLng());
        $this->setLat($report->getLat());
        $this->setAddress($report->getAddress());
        $this->setCategory($report->getCategory());

        return $this;
    }

    /**
     * Get report
     *
     * @return \AppBundle\Entity\Report
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @param mixed $solution
     * @return Report
     */
    public function setSolution($solution)
    {
        $this->solution = $solution;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSolution()
    {
        return $this->solution;
    }

    /**
     * @param mixed $reportGrades
     * @return Report
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

//    /**
//     * @return $this
//     */
//    public static function getSerializationContext()
//    {
//        return SerializationContext::create()->setGroups(self::$serializationContextArray);
//    }
}

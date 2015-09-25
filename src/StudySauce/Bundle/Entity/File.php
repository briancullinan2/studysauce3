<?php

namespace StudySauce\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="file")
 * @ORM\HasLifecycleCallbacks()
 */
class File
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="files")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\OneToOne(targetEntity="Response", mappedBy="file", fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    protected $response;

    /**
     * @ORM\Column(type="string", length=256, name="filename")
     */
    protected $filename;

    /**
     * @ORM\Column(type="string", length=256, name="upload_id")
     */
    protected $uploadId;

    /**
     * @ORM\Column(type="string", length=256, name="url", nullable=true)
     */
    protected $url;

    /**
     * @ORM\Column(type="array", name="parts", nullable=true)
     */
    protected $parts;

    /**
     * @ORM\Column(type="datetime", name="created")
     */
    protected $created;


    /**
     * @ORM\PrePersist
     */
    public function setCreatedValue()
    {
        $this->created = new \DateTime();
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set uploadId
     *
     * @param string $uploadId
     * @return File
     */
    public function setUploadId($uploadId)
    {
        $this->uploadId = $uploadId;

        return $this;
    }

    /**
     * Get uploadId
     *
     * @return string 
     */
    public function getUploadId()
    {
        return $this->uploadId;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return File
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set parts
     *
     * @param string $parts
     * @return File
     */
    public function setParts($parts)
    {
        $this->parts = $parts;

        return $this;
    }

    /**
     * Get parts
     *
     * @return string 
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return File
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set user
     *
     * @param \StudySauce\Bundle\Entity\User $user
     * @return File
     */
    public function setUser(\StudySauce\Bundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \StudySauce\Bundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return File
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set responses
     *
     * @param \StudySauce\Bundle\Entity\Response $responses
     * @return File
     */
    public function setResponses(\StudySauce\Bundle\Entity\Response $responses = null)
    {
        $this->responses = $responses;

        return $this;
    }

    /**
     * Get responses
     *
     * @return \StudySauce\Bundle\Entity\Response 
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * Set response
     *
     * @param \StudySauce\Bundle\Entity\Response $response
     * @return File
     */
    public function setResponse(\StudySauce\Bundle\Entity\Response $response = null)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get response
     *
     * @return \StudySauce\Bundle\Entity\Response 
     */
    public function getResponse()
    {
        return $this->response;
    }
}

<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_PageBuilder
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\PageBuilder\Instagram\Hydrator\Component;

class Feed
{
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $userName;

    /**
     * @var string
     */
    public $fullName;

    /**
     * @var string
     */
    public $biography;

    /**
     * @var integer
     */
    public $followers;

    /**
     * @var integer
     */
    public $following;

    /**
     * @var string
     */
    public $profilePicture;

    /**
     * @var string
     */
    public $externalUrl;

    /**
     * @var bool
     */
    public $private;

    /**
     * @var bool
     */
    public $verified;

    /**
     * @var integer
     */
    public $mediaCount = 0;

    /**
     * @var Media[]
     */
    public $medias = [];

    /**
     * @var string
     */
    public $endCursor = null;

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * @return string
     */
    public function getBiography()
    {
        return $this->biography;
    }

    /**
     * @param string $biography
     */
    public function setBiography($biography)
    {
        $this->biography = $biography;
    }

    /**
     * @return string
     */
    public function getFollowers()
    {
        return $this->followers;
    }

    /**
     * @param string $followers
     */
    public function setFollowers($followers)
    {
        $this->followers = $followers;
    }

    /**
     * @return string
     */
    public function getFollowing()
    {
        return $this->following;
    }

    /**
     * @param string $following
     */
    public function setFollowing($following)
    {
        $this->following = $following;
    }

    /**
     * @return string
     */
    public function getProfilePicture()
    {
        return $this->profilePicture;
    }

    /**
     * @param string $profilePicture
     */
    public function setProfilePicture($profilePicture)
    {
        $this->profilePicture = $profilePicture;
    }

    /**
     * @return string
     */
    public function getExternalUrl()
    {
        return $this->externalUrl;
    }

    /**
     * @param string $externalUrl
     */
    public function setExternalUrl($externalUrl)
    {
        $this->externalUrl = $externalUrl;
    }

    /**
     * @return bool
     */
    public function isPrivate()
    {
        return $this->private;
    }

    /**
     * @param bool $private
     */
    public function setPrivate($private)
    {
        $this->private = $private;
    }

    /**
     * @return bool
     */
    public function isVerified()
    {
        return $this->verified;
    }

    /**
     * @param bool $verified
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getMediaCount()
    {
        return $this->mediaCount;
    }

    /**
     * @param int $mediaCount
     */
    public function setMediaCount($mediaCount)
    {
        $this->mediaCount = $mediaCount;
    }

    /**
     * @return Media[]
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * @param Media $media
     */
    public function addMedia(Media $media)
    {
        $this->medias[] = $media;
    }

    /**
     * @return string
     */
    public function getEndCursor()
    {
        return $this->endCursor;
    }

    /**
     * @param string $endCursor
     */
    public function setEndCursor($endCursor)
    {
        $this->endCursor = $endCursor;
    }
}

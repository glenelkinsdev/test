<?php

namespace App\Dto;

use DateTime;
use Exception;

/**
 * Class EpisodeDto
 * @package App\Dto
 */
class EpisodeDto
{

    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var DateTime
     */
    private DateTime $airDate;

    /**
     * @var string
     */
    private string $code;

    /**
     * @var string
     */
    private string $url;

    /**
     * @var DateTime
     */
    private DateTime $createdAt;

    /**
     * EpisodeDto constructor.
     * @param array $rawData
     * @throws Exception
     */
    public function __construct(array $rawData)
    {
        $this->id = (int)$rawData['id'];
        $this->name = $rawData['name'];
        $this->airDate = DateTime::createFromFormat('F j, Y', $rawData['air_date']);
        $this->code = $rawData['episode'];
        $this->url = $rawData['url'];
        $this->createdAt = new DateTime($rawData['created']);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return DateTime
     */
    public function getAirDate(): DateTime
    {
        return $this->airDate;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }


}

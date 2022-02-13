<?php

namespace App\Dto;

use ArrayObject;
use DateTime;
use Exception;

/**
 * Class CharacterDto
 * @package App\Dto
 */
class CharacterDto
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
     * @var string
     */
    private string $status;

    /**
     * @var string
     */
    private string $species;

    /**
     * @var string
     */
    private string $type = 'Unknown';

    /**
     * @var string
     */
    private string $gender;

    /**
     * @var array
     */
    private array $origin;

    /**
     * @var array
     */
    private array $location;

    /**
     * @var string
     */
    private string $image;

    /**
     * @var ArrayObject
     */
    private ArrayObject $episodeIds;

    /**
     * @var string
     */
    private string $url;

    /**
     * @var DateTime
     */
    private DateTime $createdAt;

    /**
     * CharacterDto constructor.
     * @param array $rawData
     * @throws Exception
     */
    public function __construct(array $rawData)
    {

        // apply raw data with transformations where required
        $this->id = (int)$rawData['id'];
        $this->name = $rawData['name'];
        $this->status = $rawData['status'];
        $this->species = $rawData['species'];
        $this->type = !$this->type ?? $rawData['type'];
        $this->gender = $rawData['gender'];
        $this->origin = $rawData['origin'];
        $this->location = $rawData['location'];
        $this->image = $rawData['image'];
        $this->url = $rawData['url'];
        $this->createdAt = new DateTime($rawData['created']);

        // break episode urls into ids to be able to query later in bulk
        $episodeIds = array_map(function(string $url){

            // get id from url
            if(preg_match('/https?:\/\/.*\.com\/api\/episode\/([\d]+)/',$url,$matches)){
                return $matches[1];
            }

            return null;

        },$rawData['episode']);

        $this->episodeIds = new ArrayObject($episodeIds);

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
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getSpecies(): string
    {
        return $this->species;
    }


    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }


    /**
     * @return string
     */
    public function getGender(): string
    {
        return $this->gender;
    }


    /**
     * @return array
     */
    public function getOrigin(): array
    {
        return $this->origin;
    }

    /**
     * @return array
     */
    public function getLocation(): array
    {
        return $this->location;
    }


    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }


    /**
     * @return ArrayObject
     */
    public function getEpisodeIds(): ArrayObject
    {
        return $this->episodeIds;
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

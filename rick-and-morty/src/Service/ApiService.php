<?php

namespace App\Service;

use App\Dto\CharacterDto;
use App\Dto\EpisodeDto;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


/**
 * Class ApiService
 * @package App\Service
 */
class ApiService
{

    /**
     * @var string
     */
    public string $error;

    /**
     * @var string
     */
    private string $baseUrl = 'https://rickandmortyapi.com/api/';

    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $httpClient;


    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * ApiService constructor.
     * @param HttpClientInterface $httpClient
     */
    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    /**
     * @param array $filters
     * @param string $pattern
     */
    private function sanitiseStringFilters(array &$filters, string $pattern = '[^\w\s]') : void
    {
        $filters = array_map(function(string $filter) use ($pattern) {

            return preg_replace('/'.$pattern.'/','',$filter);

        },$filters);

    }

    /**
     * @param string $endPoint
     * @param int $page
     * @param array $filters
     * @return object
     */
    private function makeRequest(string $endPoint, int $page = 1, array $filters = []) : object
    {

        // response object
        $response = (object)[
            'success'=>true,
            'code'=>Response::HTTP_OK,
            'error'=>'',
            'data'=>[]
        ];

        // sanitise filters
        // this should already be done on the form level but just in case!
        $this->sanitiseStringFilters($filters);

        // make the request
        try {
            $httpResponse = $this->httpClient->request(
                'GET',
                $this->baseUrl . rtrim($endPoint,'/') . '/?page=' . $page . (!empty($filters) ? '&' . http_build_query($filters) : '')
            );

            // get status
            try{
                $response->code = $httpResponse->getStatusCode();

                // check the code
                if($response->code === Response::HTTP_TOO_MANY_REQUESTS){
                    // rate limit applied
                    $response->error = 'Rate Limits In Effect';
                    $response->success = false;
                }elseif($response->code === Response::HTTP_OK){

                    // success - get array of data
                    try {
                        $response->data = $httpResponse->toArray();
                    }catch (\Exception $exception){
                        $response->error = $exception->getMessage();
                        $response->success = false;
                        return $response;
                    }

                }elseif($response->code === Response::HTTP_NOT_FOUND) {
                    // content not found
                    $response->error = 'Content Not Found';
                    $response->success = false;
                }
            } catch (TransportExceptionInterface $exception){
                $response->error = $exception->getMessage();
                $response->success = false;
            }

        }catch (TransportExceptionInterface $exception){
            $response->error = $exception->getMessage();
            $response->success = false;
        }

        return $response;
    }

    /**
     * @param int $page
     * @param array $filters
     * @return object
     */
    public function characters(int $page = 1, array $filters = []) : object
    {

        // make the request
        $response = $this->makeRequest('character',$page,$filters);


        // if the response was a success, process the data
        if($response->success){

            // convert episode urls into episode ids for each character
            // so we can do a bulk fetch of episodes

            // create characters as dto
            $characters = array_map(function(array $characterData){

                try {
                    return new CharacterDto($characterData);
                }catch (\Exception $exception){
                    return null;
                }

            },$response->data['results']);

            // filter null characters in case date exception was thrown
            $characters = array_filter($characters,function(?CharacterDto $characterDto){

                return !is_null($characterDto);

            });

            return (object)[
                'info'=>(object)$response->data['info'],
                'characters'=>$characters,
                'success'=>true
            ];

        }

        // log the error
        $this->logger->error($response->error);

        // return nothing
        return $response;

    }

    /**
     * @param array $episodeIds
     * @return object
     */
    public function episodes(array $episodeIds) : object
    {

        // make sure all elements in episodeIds are integers
        $episodeIds = array_map('trim', $episodeIds);
        $episodeIds = array_filter($episodeIds,'is_int');


        // make the request
        $response = $this->makeRequest('episode/' . implode(',',$episodeIds));

        // if the response was a success, process the data
        if($response->success){

            // create episodes as dto
            $episodes = array_map(function(array $episodeData){

                try {
                    return new EpisodeDto($episodeData);
                }catch (\Exception $exception){
                    return null;
                }

            },$response->data['results']);

            // filter null characters in case date exception was thrown
            $episodes = array_filter($episodes,function(?EpisodeDto $episodeDto){

                return !is_null($episodeDto);

            });


            return (object)[
                'info'=>(object)$response->data['info'],
                'episodes'=>$episodes,
                'success'=>true
            ];

        }

        // log the error
        $this->logger->error($response->error);

        // return fail response
        return $response;
    }

    /**
     * @param int $characterId
     * @return object
     */
    public function character(int $characterId) : object
    {

        // make the request
        $response = $this->makeRequest('character/' . $characterId);

        // if the response was a success, process the data
        if($response->success){

            // create the dto
            try {
                $character = new CharacterDto($response->data);
            }catch (\Exception $exception){
                return (object)[
                    'error'=>$exception->getMessage(),
                    'success'=>false
                ];
            }
            return (object)[
                'character'=>$character,
                'success'=>true
            ];

        }

        // log the error
        $this->logger->error($response->error);

        // return fail response
        return $response;
    }

}

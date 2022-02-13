<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Service\ApiService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CatalogController
 * @package App\Controller
 */
class CatalogController extends AbstractController
{

    /**
     * @var int
     */
    private int $currentPage = 1;

    /**
     * @var ApiService
     */
    private ApiService $apiService;

    /**
     * CatalogController constructor.
     * @param ApiService $apiService
     */
    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * @Route ("/", name="catelog.entry", methods={"GET"})
     * @return RedirectResponse
     */
    public function index() : RedirectResponse
    {
        return $this->redirectToRoute('catalog.index');
    }

    /**
     * @Route ("/catalog/episodes/{characterId}", name="catalog.episodes", methods={"GET"})
     * @param Request $request
     * @param int $characterId
     * @return Response
     */
    public function episodes(Request $request, int $characterId) : Response
    {

        // get string of episode ids
        $episodeIdsStr = $request->query->get('episodeIds');

        // if none are present, set flash error and go back to main catalog
        if(!$episodeIdsStr){
            $this->addFlash('error','Invalid Episode Ids');
            return $this->redirectToRoute('catalog.index');
        }

        // convert to array
        $episodeIds = explode(',',$episodeIdsStr);

        // get the character
        $characterData = $this->apiService->character($characterId);
        if(!$characterData->success){
            $this->addFlash('error','Something Went Wrong: ' . $characterData->error);
        }

        // get episodes
        $episodesData = $this->apiService->episodes($episodeIds);
        if(!$episodesData->success){
            $this->addFlash('error','Something Went Wrong: ' . $episodesData->error);
            return $this->redirectToRoute('catalog.index');
        }


        // build form to filter characters from this page
        $form = $this->buildFilterForm();


        // load view
        return $this->render('catalog/episodes.html.twig',[
            'episodes'=>$episodesData->episodes,
            'character'=>$characterData->character,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route ("/catalog/{page}", name="catalog.index", methods={"GET"})
     * @param Request $request
     * @param int $page
     * @return Response
     */
    public function catalog(Request $request, int $page = 1) : Response
    {
        // set the current page
        $this->currentPage = $page;


        // build the form and check if it was submitted
        $form = $this->buildFilterForm();
        $form->handleRequest($request);

        // get the search term and any other filters
        $filters = [];
        if($form->isSubmitted() && $form->isValid()){
            $filters = $form->getData();
        }

        // get characters data
        $data = $this->apiService->characters($this->currentPage,$filters);
        if(!$data->success){
            $this->addFlash('error','Something Went Wrong: ' . $data->error);
        }


        // load view
        return $this->render('catalog/index.html.twig',[
            'characters'=>$data->success ? $data->characters : null,
            'totalPages'=>$data->success ? $data->info->pages : 1,
            'currentPage'=>$this->currentPage,
            'form'=>$form->createView(),
            'params'=>http_build_query($request->query->all())
        ]);
    }

    /**
     * @param string $route
     * @return FormInterface
     */
    private function buildFilterForm(string $route = 'catalog.index') : FormInterface
    {
        return $this->createForm(SearchType::class,null,[
            'action'=>$this->generateUrl($route),
            'method'=>'GET'
        ]);
    }


}

<?php

namespace Ydle\HubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam,
    FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use Ydle\HubBundle\Manager\LogsManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RestLogsController extends Controller
{
    /**
     * @var CategoryManagerInterface
     */
    protected $logsManager;
    protected $logger;
    protected $translator;

    public function __construct(\Ydle\HubBundle\Manager\LogsManager $logsManager, $logger, $translator)
    {
        $this->logsManager = $logsManager;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @var Request $request
     * @Post("api/log/add")
     */
    public function postApiLogAddAction(Request $request)
    {
        $message = $request->get('message');
        $level = $request->get('level');
        
        if (empty($message)) {
            $error = $this->getTranslator()->trans('log.empty.message');
            throw new HttpException(404, $error);
        }
        $this->getLogger()->log($level, $message, 'master');
        
        return 'ok';
    }

    /**
     * Retrieve the list of available node types
     *
     * @QueryParam(name="page", requirements="\d+", default="1", description="Page for node types list pagination")
     * @QueryParam(name="count", requirements="\d+", default="10", description="Number of nodes by page")
     * @QueryParam(name="type", requirements="\w+", default="", description="type of log")
     * @QueryParam(name="source", requirements="\w+", default="", description="source of log")
     *
     * @param ParamFetcher $paramFetcher
     */
    public function getLogsListAction(ParamFetcher $paramFetcher)
    {
        $page   = $paramFetcher->get('page');
        $count  = $paramFetcher->get('count');

        $pager = $this->getLogsManager()->getPager($this->filterCriteria($paramFetcher), $page, $count);

        return $pager;
    }

    /**
     *
     * @param \FOS\RestBundle\Request\ParamFetcher $paramFetcher
     */
    public function deleteLogsListAction(ParamFetcher $paramFetcher)
    {
        $this->getLogsManager()->reset();

        $message = $this->getTranslator()->trans('logs.table.empty');
        $this->getLogger()->log('info', $message, 'hub');

    }

    /**
     * Wrapper for logger
     */
    private function getLogger()
    {
        return $this->logger;
    }

    /**
     * Wrapper for nodetype manager
     */
    private function getLogsManager()
    {
        return $this->logsManager;
    }

    private function getTranslator()
    {
        return $this->translator;
    }

    /**
    * Filters criteria from $paramFetcher to be compatible with the Pager criteria
    *
    * @param ParamFetcherInterface $paramFetcher
    *
    * @return array The filtered criteria
    */
    protected function filterCriteria(ParamFetcherInterface $paramFetcher)
    {
        $criteria = $paramFetcher->all();

        unset($criteria['page'], $criteria['count']);

        foreach ($criteria as $key => $value) {
            if (null === $value) {
                unset($criteria[$key]);
            }
        }

        return $criteria;
    }

}

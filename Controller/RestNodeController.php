<?php

namespace Ydle\HubBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam,
    FOS\RestBundle\Request\ParamFetcherInterface;
use Ydle\HubBundle\Entity\NodeData;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class RestNodeController extends Controller
{
    /**
     * @var CategoryManagerInterface
     */
    protected $nodeManager;

    protected $container;
    
    protected $nodeTypeManager;
    protected $nodeDataManager;
    protected $logger;
    protected $translator;
    protected $doctrine;
    protected $masterAddress;
    protected $masterId;

    public function __construct(\Ydle\HubBundle\Manager\NodeManager $nodeManager, 
                                $nodeTypeManager, $nodeDataManager, $logger, $translator, $doctrine,
                                $masterAddress, $masterId)
    {
        $this->nodeManager = $nodeManager;
        $this->nodeTypeManager = $nodeTypeManager;
        $this->nodeDataManager = $nodeDataManager;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->doctrine = $doctrine;
        $this->masterAddress = $masterAddress;
        $this->masterId = $masterId;
    }

    /**
     * Retrieve the list of available nodes 
     *
     * @QueryParam(name="page", requirements="\d+", default="0", description="Number of page")
     * @QueryParam(name="count", requirements="\d+", default="0", description="Number of nodes by page")
     *
     * @param ParamFetcher $paramFetcher
     */
    public function getNodesListAction(ParamFetcher $paramFetcher)
    {
        $page  = $paramFetcher->get('page');
        $count = $paramFetcher->get('count');

        $pager = $this->getNodeManager()->getPager($this->filterCriteria($paramFetcher), $page, $count);

        return $pager;
    }

    /**
     * Retrieve the list of available nodes
     *
     * @QueryParam(name="room_id", requirements="\d+", default="0", description="Id of the room")
     * @QueryParam(name="page", requirements="\d+", default="0", description="Number of page")
     * @QueryParam(name="count", requirements="\d+", default="0", description="Number of nodes by page")
     *
     * @param ParamFetcher $paramFetcher
     */
    public function getRoomNodesListAction(ParamFetcher $paramFetcher)
    {
        $page   = $paramFetcher->get('page');
        $count  = $paramFetcher->get('count');

        $pager = $this->getNodeManager()->getPager($this->filterCriteria($paramFetcher), $page, $count);

        return $pager;
    }

    public function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * Wrapper for node manager
     */
    private function getNodeManager()
    {
        return $this->nodeManager;
    }

    /**
     * Wrapper for node manager
     */
    private function getNodeTypeManager()
    {
        return $this->nodeTypeManager;
    }

    private function getNodeDataManager()
    {
        return $this->nodeDataManager;
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

    /**
     *
     * @QueryParam(name="node_id", requirements="\d+", default="0", description="Id of the node")
     * @QueryParam(name="state", requirements="\d+", default="1", description="New state for this node")
     *
     * @param \FOS\RestBundle\Request\ParamFetcher $paramFetcher
     */
    public function putNodeStateAction(ParamFetcher $paramFetcher)
    {
        $nodeId = $paramFetcher->get('node_id');
        $state = $paramFetcher->get('state');

        if (!$result = $this->getNodeManager()->changeState($nodeId, $state)) {
            $message = $this->getTranslator()->trans('node.not.found');
            throw new HttpException(404, $message);
        }
        if ($state == 1) {
            $message = $this->getTranslator()->trans('node.activate.success');
            $this->getLogger()->log('info', $message, 'hub');
        } elseif ($state == 0) {
            $message = $this->getTranslator()->trans('node.deactivate.success');
            $this->getLogger()->log('info', $message, 'hub');
        }

        return new JsonResponse('Node status changed successfully', 200);
    }



    /**
     * @var Request $request
     */
    public function postNodesDatasAction(Request $request)
    {
        $sender   = $request->get('sender');
        $type     = $request->get('type');
        $data     = $request->get('data');

        if (!$node = $this->getNodeManager()->findOneBy(array('code' => $sender))) {
            $message = $this->getTranslator()->trans('node.not.found');
            throw new HttpException(404, $message);
        }

        if (!$type = $this->getNodeTypeManager()->find($type)) {
            $message = $this->getTranslator()->trans('nodetype.not.found');
            throw new HttpException(404, $message);
        }

        if (empty($data)) {
            $message = $this->getTranslator()->trans('data.not.found');
            throw new HttpException(404, $message);
        }

        $nodeData = new NodeData();
        $nodeData->setNode($node);
        $nodeData->setData($data);
        $nodeData->setType($type);

        $this->getNodeDataManager()->save($nodeData);

        $this->getLogger()->log('data', 'Data received from node #'.$sender.' : '.$data, 'node');

        return new JsonResponse(array('code' => 0, 'result' => 'data sent'));

    }

    /**
     *
     * @QueryParam(name="node", requirements="\d+", default="0", description="Code of the node")
     * @QueryParam(name="filter", requirements="\w+", default="day", description="date filter")
     *
     * @param \FOS\RestBundle\Request\ParamFetcher $paramFetcher
     */
    public function getRoomNodeStatsAction(ParamFetcher $paramFetcher)
    {
        $code = $paramFetcher->get('node');
        $filter = $paramFetcher->get('filter');

        if (!$node = $this->getNodeManager()->findOneBy(array('code' => $code))) {
            $message = $this->getTranslator()->trans('node.not.found');
            throw new HttpException(404, $message);
        }

        // Manage starting date
        $startTime = 0;
        switch ($filter) {
            case 'month':
                $startTime = strtotime("-1 month");
                break;
            case 'week':
                $startTime = strtotime("-1 week");
                break;
            default:
            case 'day':
                $startTime = strtotime("-1 day");
                break;
        }
        $startDate = new \DateTime();
        $startDate->setTimestamp($startTime);

        $params = array(
            'node_id' => $node->getId(),
            'room_id' => $node->getRoom()->getId(),
        'start_date' => $startDate
        );
        $datas = $this->getNodeDataManager()->findByParams($params);

        $result = array();
        $cpt = 1;
        foreach ($datas as $data) {
            $type = $data->getType();
            if (empty($result[$type->getId()])) {
                $result[$type->getId()] = array(
                    'label' => $type->getName().' ('. $type->getUnit().')',
                    'data' => array(),
                    'yaxis' => $cpt
                );
            }
            $value = $data->getData();
            switch ($type->getUnit()) {
                case '°C':
                case '%':
                    $value = round($value / 100, 1);
            }
            $result[$type->getId()]['data'][] = array((int) $data->getCreated()->format('U') * 1000, $value);
            $cpt++;
        }

        return new JsonResponse($result);
    }
        
    /**
     *
     * @QueryParam(name="node", requirements="\d+", default="0", description="Code of the node")
     * @QueryParam(name="type", requirements="\d+", default="day", description="Id of the data type")
     *
     * @param \FOS\RestBundle\Request\ParamFetcher $paramFetcher
     */
    public function getNodeLastDataAction(ParamFetcher $paramFetcher)
    {
        $code = $paramFetcher->get('node');
        $type = $paramFetcher->get('type');

        if (!$node = $this->getNodeManager()->findOneBy(array('code' => $code))) {
            $message = $this->getTranslator()->trans('node.not.found');
            throw new HttpException(404, $message);
        }

        if (!$type = $this->getNodeTypeManager()->find($type)) {
            $message = $this->getTranslator()->trans('nodetype.not.found');
            throw new HttpException(404, $message);
        }
        
        $return = array(
            'data' => null,
            'date' => null,
            'unit' => null
        );
        $params = array('type_id' => $type);
        $lastData = $this->getNodeDataManager()->getNodeLastData($node->getId(), $params);
        
        if(!empty($lastData)){
            $return = array(
                'data' => $lastData->getData(),
                'date' => (int)$lastData->getCreated()->format("U"),
                'unit' => $lastData->getType()->getUnit()
            );
        }
        return new JsonResponse($return);
    }

    private function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Wrapper for logger
     */
    private function getLogger()
    {
        return $this->logger;
    }
    
    /**
     * Wrapper for master address
     * 
     * @return string
     */
    private function getMasterAddress()
    {
        return $this->masterAddress;
    }
    
    /**
     * Wrapper for master Id
     * 
     * @return integer
     */
    private function getMasterId()
    {
        return $this->masterId;
    }

    /**
     *
     * @QueryParam(name="node", requirements="\d+", default="0", description="Id of the node")
     *
     * @param \FOS\RestBundle\Request\ParamFetcher $paramFetcher
     */
    public function putNodeLinkAction(ParamFetcher $paramFetcher)
    {
        $nodeId = $paramFetcher->get('node');

        if (!$node = $this->getNodeManager()->find($nodeId)) {
            $message = $this->getTranslator()->trans('node.not.found');
            return new JsonResponse('No node found', 404);
        }
        // Check if the required options are set in the parameters.yml file
        $masterAddr = $this->getMasterAddress();
        $masterCode = $this->getMasterId();
        if (empty($masterAddr) || empty($masterAddr)) {
            $message = $this->getTranslator()->trans('node.link.fail.nomaster');
            $this->get('session')->getFlashBag()->add('error', $message);
            return new JsonResponse('No master address found', 404);
        }

        $address = $masterAddr;
        $address .= ':8888/node/link?target='.$node->getCode().'&sender=';
        $address .= $masterCode;

        $ch = curl_init($address);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
        $message = $this->getTranslator()->trans('node.link.success', array('%nodeCode%' => $node->getCode()));
        $this->getLogger()->log('info', $message);

        return new JsonResponse('Node linked', 200);
    }
    
    /**
     *
     * @QueryParam(name="node", requirements="\d+", default="0", description="Id of the node")
     *
     * @param \FOS\RestBundle\Request\ParamFetcher $paramFetcher
     */
    public function deleteNodeAction(ParamFetcher $paramFetcher)
    {
        $nodeId = $paramFetcher->get('node');

        if (!$node = $this->getNodeManager()->find($nodeId)) {
            return new JsonResponse('This node does not exist', 404);
        }
        
        if($node->getIsActive()){
            return new JsonResponse('This node is still active', 403);
        }
        
        $dataParams = array('node_id' => $nodeId, 'limit' => 1);
        $currentData = $this->getNodeDataManager()->findByParams($dataParams);
        if(!empty($currentData)){
            return new JsonResponse('This node still has data', 403);
        }
        $result = $this->getNodeManager()->delete($node);

        return new JsonResponse('Node deleted successfully', 200);
    }
    
    /**
     *
     * @QueryParam(name="node", requirements="\d+", default="0", description="Id of the node")
     *
     * @param \FOS\RestBundle\Request\ParamFetcher $paramFetcher
     */
    public function deleteNodeDataAction(ParamFetcher $paramFetcher)
    {
        $nodeId = $paramFetcher->get('node');

        if (!$node = $this->getNodeManager()->find($nodeId)) {
            return new JsonResponse('This node does not exist', 404);
        }
        
        $result = $this->getNodeDataManager()->deleteNodeData($node->getId());

        return new JsonResponse('Node flush successfully', 200);
    }
}

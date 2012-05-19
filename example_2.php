<?php
// sql version? 

require_once 'lib.php';

function get_plain_tree($id){
        return array(
                array('id'=>5,'father_id'=>3138,'mother_id'=>59),
                array('id'=>3138,'father_id'=>184,'mother_id'=>593),
                array('id'=>59,'father_id'=>586,'mother_id'=>587),
                array('id'=>184,'father_id'=>408,'mother_id'=>409),
                array('id'=>593,'father_id'=>408,'mother_id'=>588),
                array('id'=>586,'father_id'=>151,'mother_id'=>370),
                array('id'=>587,'father_id'=>408,'mother_id'=>19199),
                array('id'=>408,'father_id'=>410,'mother_id'=>411),
                array('id'=>409,'father_id'=>37293,'mother_id'=>413),
                array('id'=>588,'father_id'=>34848,'mother_id'=>19202),
                array('id'=>151,'father_id'=>153,'mother_id'=>154),
                array('id'=>370,'father_id'=>19201,'mother_id'=>589),
                array('id'=>19199,'father_id'=>151,'mother_id'=>590),
                array('id'=>410,'father_id'=>20583,'mother_id'=>50802),
                array('id'=>411,'father_id'=>410,'mother_id'=>21433),
                array('id'=>37293,'father_id'=>21364,'mother_id'=>39010),
                array('id'=>413,'father_id'=>42369,'mother_id'=>52506),
                array('id'=>34848,'father_id'=>19245,'mother_id'=>21443),
                array('id'=>19202,'father_id'=>37293,'mother_id'=>21445),
                array('id'=>153,'father_id'=>19189,'mother_id'=>21383),
                array('id'=>154,'father_id'=>40759,'mother_id'=>44890),
                array('id'=>19201,'father_id'=>21446,'mother_id'=>21433),
                array('id'=>589,'father_id'=>21404,'mother_id'=>45920),
                array('id'=>590,'father_id'=>493,'mother_id'=>36590),
                array('id'=>20583,'father_id'=>52345,'mother_id'=>20585),
                array('id'=>50802,'father_id'=>20865,'mother_id'=>41342),
                array('id'=>21433,'father_id'=>20583,'mother_id'=>50802),
                array('id'=>21364,'father_id'=>21366,'mother_id'=>21365),
                array('id'=>39010,'father_id'=>21366,'mother_id'=>21365),
                array('id'=>42369,'father_id'=>20885,'mother_id'=>21435),
                array('id'=>52506,'father_id'=>21366,'mother_id'=>63096),
                array('id'=>19245,'father_id'=>600,'mother_id'=>98496));
}

function get_itm_tree($id, $data = null, $ids = null){
        if(is_null($data))
                $data = get_plain_tree($id);
        if(is_null($ids)){
                $ids = array();
                foreach($data as $v)
                        $ids[$v['id']] = $v;
        }
        
        if(!isset($ids[$id]))
                return;

        $itm = $ids[$id];
        $r = array('id'=>$id);

        if($it = get_itm_tree($itm['mother_id'], $data, $ids))
                $r['mother'] = $it;

        if($it = get_itm_tree($itm['father_id'], $data, $ids))
                $r['father'] = $it;
        
        return $r;
}

function translate($tree, $top = null){
	$node = node();
	$node->self = $tree['id'];
	$node->top = $top;

	if(isset($tree['mother']))
		$node->car = translate($tree['mother'], $node);
	if(isset($tree['father']))
		$node->cdr = translate($tree['father'], $node);
	return $node;
}

foreach(calc_inbreeding(translate(get_itm_tree(5))) as $v)
	echo "Object with id: ".$v['node']->self.' have COI: '.$v['num']."%\n";


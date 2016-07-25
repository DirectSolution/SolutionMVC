<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel;

/**
 * Description of ReviewFrequencies
 *
 * @author dhayward
 */
class ReviewFrequencies Extends BaseModel {

    public function getOne($id){
        return $this->prod_audit->ReviewFrequencies[$id];
    }
    
    public function getAll($client) {
        return
                array_map('iterator_to_array', iterator_to_array(
                        $this->prod_audit->ReviewFrequencies
                                ->where("client", $client)
                                ->and("retired", 0)
                                ->or("client", 0)
                                ->and("retired", 0)
                )
        );
    }

}

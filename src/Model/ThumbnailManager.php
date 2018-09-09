<?php
/**
 * Created by PhpStorm.
 * User: connector
 * Date: 16/07/2018
 * Time: 16:44
 */

namespace App\Model;

use App\Entity\Thumbnails;
use PDO;

class ThumbnailManager extends Manager
{
    public function  __construct()
    {
        parent::__construct('thumbnails');
    }

    public function create(&$thumbnail){
        $this->pdostatement=$this->pdo->prepare('REPLACE INTO thumbnails VALUES (NULL,:image_id,:dirname,:basename)');
        //liaison des paramètres

        $this->pdostatement->bindValue(':image_id',$thumbnail->getImagesId(),PDO::PARAM_INT);
        $this->pdostatement->bindValue(':dirname',$thumbnail->getDirname(),PDO::PARAM_STR);
        $this->pdostatement->bindValue(':basename',$thumbnail->getBasename(),PDO::PARAM_STR);

        //execution de la requête
        $executeIsOk=$this->pdostatement->execute();

        if (!$executeIsOk){
            return false;
        }
        else{
            $id=$this->pdo->lastInsertId();
            $thumbnail= $this->read($id);
            return $thumbnail;


        }



    }

    public function read($imageId)
    {
        $this->pdostatement = $this->pdo->prepare('SELECT * FROM thumbnails WHERE images_id = :imageId');
        //liaison paramètres
        $this->pdostatement->bindValue(':imageId', $imageId, PDO::PARAM_INT);
        //Execution de la requête
        $executeIsOk = $this->pdostatement->execute();
        $thumbnails = [];
        while ($thumbnail = $this->pdostatement->fetchObject("App\Entity\Thumbnails")) {
            $thumbnails[] = $thumbnail;
        }

        return $thumbnails;
    }
}
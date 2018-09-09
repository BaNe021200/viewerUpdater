<?php
/**
 * Created by PhpStorm.
 * User: connector
 * Date: 16/07/2018
 * Time: 17:16
 */

namespace App\Model;
Use App\Entity\Images;
use PDO;

class ImageManager extends Manager
{
    public function  __construct()
    {
        parent::__construct('images');
    }

    public function create(&$image){
        $this->pdostatement=$this->pdo->prepare('REPLACE INTO images VALUES (NULL,:dirname,:basename)');
        //liaison des paramètres


        $this->pdostatement->bindValue(':dirname',$image->getDirname(),PDO::PARAM_STR);
        $this->pdostatement->bindValue(':basename',$image->getBasename(),PDO::PARAM_STR);

        //execution de la requête
        $executeIsOk=$this->pdostatement->execute();

        if (!$executeIsOk){
            return false;
        }
        else {
            $id = $this->pdo->lastInsertId();
            $image = $this->read($id);
            return $image;
        }











    }
    public function read($image)
    {
        $this->pdostatement = $this->pdo->prepare('SELECT * FROM images WHERE id = :imageId');
        //liaison paramètres
        $this->pdostatement->bindValue(':imageId', $image, PDO::PARAM_INT);
        //Execution de la requête
        $executeIsOk = $this->pdostatement->execute();
        $thumbnails = [];
        while ($thumbnail = $this->pdostatement->fetchObject("App\Entity\Thumbnails")) {
            $thumbnails[] = $thumbnail;
        }

        return $thumbnails;
    }
}
<?php

namespace App\Controller;

use App\Model\ImageManager;
use App\Model\ThumbnailManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Thumbnails;
use App\Entity\Images;
use App\Controller\ImagesController;

class DownloadController extends Controller
{

    public function download($item)
    {
        return $this->render('images/'.$item.'/download'.$item.'.html.twig',[

        ]);
    }


    public function upload($item)
    {
        $messages=[];

        foreach ($_FILES as $file)
        {
            if($file['error']== UPLOAD_ERR_NO_FILE)
            {
                continue;
            }
            if (is_uploaded_file($file['tmp_name'])) {
                //on vérifie que le fichier est d'un type autorisé
                $typeMime = mime_content_type($file['tmp_name']);
                if ($typeMime == 'image/jpeg') {
                    //on verifie la taille du fichier
                    $size = filesize($file['tmp_name']);
                    if ($size > 1600000) {
                        $messages[] = "le fichier est trop gros";
                    } else {

                        $destinationPath = 'img/' . $item . '/' . $file['name'];
                        $temporaryPath = $file['tmp_name'];
                        if (move_uploaded_file($temporaryPath, $destinationPath)) {
                            $messages[] = "le fichier " . $file['name'] . " a été correctement uploadé";
                            $entityManager = $this->getDoctrine()->getManager();
                            $image = new Images();


                            $image->setDirname('img/' . $item);
                            $image->setBasename($file['name']);
                            $entityManager->persist($image);
                            $entityManager->flush();
                            $imageId = $image->getId();

                            $thumbnail = new Thumbnails();
                            $thumbnail->setImagesId($imageId);
                            $thumbnail->setDirname('img/' . $item . '/thumbs/');
                            $thumbnail->setBasename($file['name']);
                            $entityManager->persist($thumbnail);
                            $entityManager->flush();


                        } else {
                            $messages[] = "le fichier " . $file['name'] . " n'a pas été correctement uploadé";
                        }
                        //$this->thumbNails(500,300,$item);
                        $this->cropImagesUpload($item);
                        //$this->cropUpdate($item);


                    }
                }else{
                    $messages[] = 'type de fichiers non valide';
                }
                }else{
                if($file['error']==2){$messages[]= 'votre fichier est trop volumineux';}
                if($file['error']==1){$messages[]= 'votre fichier excède la taille de configuration du serveur.Veuillez Uploader un fichier < à 1.4mo ';}

            }
            }
        return $this->render('images/'.$item.'/success'.$item.'.html.twig',[
            'message' => $messages,
        ]);
    }



    public function getImages($item){
       return $this->cropUpdate($item);


    }

    public function cropImages($item,$pictures){



        $images=$pictures;
        foreach ($images as $image){


            $src= $image;
            $infoName= pathinfo($src);
            $cropName=$infoName['basename'];
            $image = imagecreatefromjpeg($src);
            $size = min(imagesx($image), imagesy($image));
            $im2 = imagecrop($image, ['x' => 0, 'y' => 0, 'width' => $size, 'height' => $size]);
            if ($im2 !== FALSE) {
                imagejpeg($im2, 'img/'.$item.'/thumbs/' . $cropName);
            }
                $entityManager = $this->getDoctrine()->getManager();
                $image = new Images();


                $image->setDirname('img/'.$item);
                $image->setBasename($cropName);
                $entityManager->persist($image);
                $entityManager->flush();
                $imageId= $image->getId();

                $thumbnail = new Thumbnails();
                $thumbnail->setImagesId($imageId);
                $thumbnail->setDirname('img/'.$item.'/thumbs/');
                $thumbnail->setBasename($cropName);
                $entityManager->persist($thumbnail);
                $entityManager->flush();



                if ($thumbnail)
                {
                    $messages[]= "vos photos sont à jour";
                }else
                {
                    $messages[]="la mise à jour a échoué";
                }

                return $this->render('images/'.$item.'/success'.$item.'.html.twig',[
                    'message' => $messages,
                ]);




        }

    }

    public function cropImagesUpload($item){



        $images=glob('img/'.$item.'/*.jpg');



        foreach ($images as $image){


            $src= $image;
            $infoName= pathinfo($src);
            $cropName=$infoName['basename'];
            $image = imagecreatefromjpeg($src);
            $size = min(imagesx($image), imagesy($image));
            $im2 = imagecrop($image, ['x' => 0, 'y' => 0, 'width' => $size, 'height' => $size]);
            if ($im2 !== FALSE) {
                imagejpeg($im2, 'img/'.$item.'/thumbs/' . $cropName);




            }
        }

    }

    public function cropImagesUploadForUpdate($item){



        $images=glob('img/'.$item.'/*.jpg');


        $messages=[];
        foreach ($images as $image){


            $src= $image;
            $infoName= pathinfo($src);
            $cropName=$infoName['basename'];
            $image = imagecreatefromjpeg($src);
            $size = min(imagesx($image), imagesy($image));
            $im2 = imagecrop($image, ['x' => 0, 'y' => 0, 'width' => $size, 'height' => $size]);
            if ($im2 !== FALSE) {
                imagejpeg($im2, 'img/'.$item.'/thumbs/' . $cropName);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $image = new Images();


            $image->setDirname('img/'.$item);
            $image->setBasename($cropName);
            $entityManager->persist($image);
            $entityManager->flush();
            $imageId= $image->getId();

            $thumbnail = new Thumbnails();
            $thumbnail->setImagesId($imageId);
            $thumbnail->setDirname('img/'.$item.'/thumbs/');
            $thumbnail->setBasename($cropName);
            $entityManager->persist($thumbnail);
            $entityManager->flush();
        }
        if($thumbnail){
            $messages[]="Vos photos sont maintenant à jours";
        }else{
            $messages[]="Une erreur est survenue, imossible de mettre vos photos à jour";
        }
        return $this->render('images/' . $item . '/success' . $item . '.html.twig', [
            'message' => $messages,
        ]);

    }

    public function cropButton(){



        $images=glob('img/crops/*.jpg');var_dump($images);
        foreach ($images as $image){


            $src= $image;
            $infoName= pathinfo($src);
            $cropName=$infoName['basename'];
            $image = imagecreatefromjpeg($src);
            $size = min(imagesx($image), imagesy($image));
            $im2 = imagecrop($image, ['x' => 0, 'y' => 0, 'width' => $size, 'height' => $size]);
            if ($im2 !== FALSE) {
                imagejpeg($im2, 'img/crops/cropThumbs/' . $cropName);

                /*$cropFile = new App\Model\Projet5_images();
                $cropFile
                    ->setUserId(intval($_COOKIE['ID']))
                    ->setDirname('users/img/user/'.$_COOKIE['username'].'/crops')
                    ->setFilename($cropName.'-cropped')
                    ->setExtension('jpg');
                $cropManager=new App\Model\ImagesManager();
                $addCroppedFile = $cropManager->create($cropFile);*/






            }
        }
        return $this->render('download.html.twig');
    }

    public function cropUpdate($item)
    {

        $messages = [];
        $imgs = glob('img/' . $item . '/*.jpg');
        foreach ($imgs as $img) {
            $imgBasename[] = basename($img);
            $srcSize= filesize($img);

            if($srcSize['error']==2){$messages[] = "Le fichier ".$imgBasename." est trop volumineux";}
            if($srcSize['error']==1){$messages[]= 'votre fichier excède la taille de configuration du serveur.Veuillez Uploader un fichier < à 1.4mo ';}
        }

        //var_dump($imgBasename);die;
        $thumbs = glob('img/' . $item . '/thumbs/*.jpg');
        foreach ($thumbs as $thumb) {
            $thumbBasename[] = basename($thumb);
        }



        @$data = array_diff(@$imgBasename, @$thumbBasename);

        if(empty($imgBasename)){
            $messages[]="Votre dossier photos est vide";
            $explo=  exec("C:\WINDOWS\\explorer.exe /e,/select,C:\wamp64\www\PhpTraining\\viewverUpDater\public\img\\".$item."\\thumbs");
            return $this->render('images/' . $item . '/success' . $item . '.html.twig', [
                'message' => $messages,
                
            ]);
        }elseif(empty($thumbBasename)){

            return $this->cropImagesUploadForUpdate($item);



        }else
            {



        foreach ($data as $datum) {
            $pictures[] = 'img/' . $item . '/' . $datum; //var_dump($data);die;
        }


        if (!empty($data)) {



            $images = $pictures;
            foreach ($images as $image) {


                $src = $image;
                $srcName = basename($src);
                $srcSize= filesize($src);
                if($srcSize > 1600000 )
                {$messages[] =nl2br("Le fichier ".$srcName." est trop volumineux\r\n ");


                }else {

                    $infoName = pathinfo($src);
                    $cropName = $infoName['basename'];//var_dump($image);die;
                    $image = imagecreatefromjpeg($src);
                    $size = min(imagesx($image), imagesy($image));
                    $im2 = imagecrop($image, ['x' => 0, 'y' => 0, 'width' => $size, 'height' => $size]);
                    if ($im2 !== FALSE) {
                        imagejpeg($im2, 'img/' . $item . '/thumbs/' . $cropName);
                    }

                    $entityManager = $this->getDoctrine()->getManager();
                    $image = new Images();


                    $image->setDirname('img/' . $item);
                    $image->setBasename($cropName);
                    $entityManager->persist($image);
                    $entityManager->flush();
                    $imageId = $image->getId();

                    $thumbnail = new Thumbnails();
                    $thumbnail->setImagesId($imageId);
                    $thumbnail->setDirname('img/' . $item . '/thumbs/');
                    $thumbnail->setBasename($cropName);
                    $entityManager->persist($thumbnail);
                    $entityManager->flush();
                    if(@$thumbnail)
                    {
                        $messages[]= nl2br("le fichier ".$srcName." a été uploadé\r\n");
                    }

                }



            }

        }else {
            $messages[] = "Il n'y a rien à mettre à jour";
        }
            return $this->render('images/' . $item . '/success' . $item . '.html.twig', [
                'message' => $messages,
            ]);
        }
    }

    public function downloadItem2_1()
    {

        return ($this->download("Item2_1"));
    }

    public function downloadFursMen()
    {

        return ($this->download("fursMen"));
    }

    public function uploadItem2_1()
    {
        return($this->upload("Item2_1"));
    }

    public function uploadFursMen()
    {
        return($this->upload("fursMen"));
    }

    public function downloadItem1()
    {

        return ($this->download("Item1"));
    }

    public function uploadItem1()
    {
        return($this->upload("Item1"));
    }




    public function updateItem1()
    {
        return $this->getImages('item1');

    }


    public function updateItem2_1()
    {
        return $this->getImages('item2_1');

    }

    public function updateItem2_2()
    {
        return $this->getImages('item2_2');

    }




}

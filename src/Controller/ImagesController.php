<?php

namespace App\Controller;

use App\Entity\Thumbnails;
use App\Entity\Images;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Controller\DownloadController;
use App\Model\ThumbnailManager;
use App\Model\ImageManager;

class ImagesController extends Controller
{




    public function index()
    {
        return $this->render('images/Doudounes/item1.html.twig', [
            'controller_name' => 'ImagesController',
        ]);
    }

    public function item($item)
    {

        $dir = 'img/'.$item.'/thumbs/';


        $bg_ramdom2 = mt_rand(1, 2);
        $bg_ramdom3 = mt_rand(1, 3);
        $bg_ramdom6 = mt_rand(1, 6);

        $thumbs = $this->getDoctrine()
            ->getRepository(Thumbnails::class)
            ->findByDirname($dir);


        return $this->render('images/'.$item.'/'.$item.'.html.twig',[

            'thumbs' => $thumbs,
            'bg_ramdom' => $bg_ramdom2,
            'bg_ramdom3' => $bg_ramdom3,
            'bg_ramdom6' =>$bg_ramdom6,


        ]);
    }

    public function item2_1()
    {
        return ($this->item('Item2_1'));
    }

    public function item2_2()
    {
        return ($this->item('Item2_2'));
    }



    public function item1()
    {
        return ($this->item('Item1'));
    }



    public function openExplo($item)
    {
     $explo=  exec("C:\WINDOWS\\explorer.exe /e,/select,C:\wamp64\www\PhpTraining\pinterest\pinterest2\public\img\\".$item."\\thumbs");

        return $this->redirect($this->item($item));


    }





}

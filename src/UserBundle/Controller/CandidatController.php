<?php

namespace UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Candidat;
/**
 * Offre controller.
 *
 * @Route("candidat")
 */
class CandidatController extends Controller
{

    /**
     * Page d'inscription des candidats.
     *
     * @Route("/inscription", name="candidat_new")
     * @Method({"GET", "POST"})
     */
    public function newCandidat(Request $request)
    { 
        
        $candidat = new Candidat();
        $form = $this->createForm('UserBundle\Form\CandidatType', $candidat);
        $form->handleRequest($request);
        $candidat->setRole(array("ROLE_CANDIDAT"));
        $nomSkill = $this->getDoctrine()->getRepository('UserBundle\Entity\Skill')->findAll();
        
        
        
        
        if ($form->isSubmitted() && $form->isValid()) {
            $image = md5(uniqid()) . "." . $candidat->getImage()->guessExtension();
            $candidat->getImage()->move('../web/uploads', $image);
            $candidat->setImage($image);
                        
            $em = $this->getDoctrine()->getManager();
            $candidat->setDateInscription(date("d/m/Y"));
            $em->persist($candidat);
            $em->flush($candidat);

            return $this->redirectToRoute('indexCandidat', array('id' => $candidat->getId()));
        }

        return $this->render('candidat/newCandidat.html.twig', array(
            'candidat' => $candidat,
            'form' => $form->createView(),
            'nomSkill' => $nomSkill,
        ));
    }

//    /**
//     * Finds and displays a candidat entity.
//     *
//     * @Route("candidat/index/{id}", name="candidat_show")
//     * @Method("GET")
//     */
//    public function showAction(Candidat $candidat)
//    {
//        return $this->render('candidat/showCandidat.html.twig', array(
//            'candidat' => $candidat
//        ));
//    }
    
    //Index Candidat
    /**
     * @Route("/profil", name="indexCandidat")
     */
    public function Candidat  () {
        $em = $this->getDoctrine()->getManager();

        $candidat = $em->getRepository('UserBundle:Candidat')->find($this->getUser()->getId());
        return $this->render('candidat/showCandidat.html.twig', array(
                    'candidat' => $candidat 
        ));
    }


    /**
     * Displays a form to edit an existing candidat entity.
     *
     * @Route("/modifier/{id}", name="candidat_edit")
     * @Method({"GET", "POST"})
     */
    public function editProfilCandidat(Request $request, Candidat $candidat)
    {
//        $deleteForm = $this->createDeleteForm($candidat);
        $editForm = $this->createForm('UserBundle\Form\CandidatType', $candidat);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('candidat_edit', array('id' => $candidat->getId()));
        }

        return $this->render('candidat/editCandidat.html.twig', array(
            'candidat' => $candidat,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * @Route("/listeprofilcandidat", name="ListeCandidats")
     */
    public function viewAllCandidat (){
        
         return $this->render('candidat/listeAllCandidat.html.twig');
        
        
    }
    /**
     * @Route("/profil/like")
     */
    public function tartempion(){
        $em = $this->getDoctrine()->getManager();
        $candidat = $em->getRepository('UserBundle:Candidat')->find($this->getUser()->getId());
//        $candidat = $em->getRepository('UserBundle:Candidat')->find(1);
        $r = $candidat->getRecruteurs();
        return new JsonResponse(count($r));
    }

    
}


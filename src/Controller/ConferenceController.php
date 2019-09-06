<?php

namespace App\Controller;

use App\Entity\Vote;
use App\Form\ConferenceType;
use App\Entity\Conference;
use App\Form\SearchType;
use App\Form\VoteType;
use App\Repository\ConferenceRepository;
use App\Repository\VoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConferenceController extends AbstractController
{
    /**
     * @Route("/conferenceAdd", name="conferenceAdd")
     */
    public function conferenceAdd(Request $request)
    {
        $conference = new Conference();
        $form = $this->createForm(ConferenceType::class,$conference);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $conference->setCreator($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($conference);
            $entityManager->flush();
        }

        return $this->render('conference/conferenceAdd.html.twig', [
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/home", name="home")
     */
    public function index(ConferenceRepository $conferenceRepository)
    {
        if (isset($_GET['wordToSearch'])){

            $wordToSearch = $_GET['wordToSearch'];

            $conferences= $conferenceRepository->createQueryBuilder('c')
                ->where('c.title LIKE :wordToSearch')
                ->setParameter('wordToSearch', '%'.$wordToSearch.'%')
                ->getQuery()
                ->getResult()
            ;

        }else{
            $conferences = $conferenceRepository->findAll();
        }
            $averageNote = [];
            foreach ($conferences as $conference)
            {
                $values = [];
                $votes = $conference->getVotes();
                foreach ($votes as $vote)
                {
                    $values[] = $vote->getValue();
                }
                if (!empty($values))//check if $values is empty to evoid dividing by 0
                {
                    //dd($values);
                    $average = array_sum($values)/count($values);
                    $averageNote[$conference->getId()] = $average;
                }
                else
                {
                    $averageNote[$conference->getId()] = "pas encore de note";
                }

        }


        return $this->render('conference/index.html.twig',  [

            'conferences' => $conferences,
            'average'     => $averageNote,
        ]);
    }

    /**
     * @Route("/conference/{id}", name="conference-show")
     */
    public function oneConference(int $id,ConferenceRepository $conferenceRepository,Request $request, VoteRepository $voteRepository)
    {
        $conference = $conferenceRepository->findOneBy(['id' => $id]);
        $vote= new Vote();
        $form = $this->createForm(VoteType::class, $vote);
        $form->handleRequest($request);
        $voted = $voteRepository->findOneBy(['id' => $this->getUser()]);
        if ($form->isSubmitted() && $form->isValid())
        {
            $vote->setUser($this->getUser());
            $vote->setConference($conference);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($vote);
            $entityManager->flush();
        }

        return $this->render('conference/confSpe.html.twig', [
            'conference' => $conference,
            'vote'       => $vote,
            'form'       => $form->createView()
        ]);

    }
    /**
     * @Route("/votedConf", name="votedConferences")
     */
    public function votedConf(VoteRepository $voteRepository,ConferenceRepository $conferenceRepository)
    {
        $voted = $voteRepository->findBy(['user' => $this->getUser()->getId()]);
        $conferences = $conferenceRepository->findAll();
        $averageNote = [];
        foreach ($conferences as $conference)
        {
            $values = [];
            $votes = $conference->getVotes();
            foreach ($votes as $vote)
            {
                $values[] = $vote->getValue();
            }
            if (!empty($values))//check if $values is empty to evoid dividing by 0
            {
                //dd($values);
                $average = array_sum($values) / count($values);
                $averageNote[$conference->getId()] = $average;
            } else {
                $averageNote[$conference->getId()] = "pas encore de note";
            }
        }

        return $this->render('conference/votedConf.html.twig', [

            'average'     => $averageNote,
            'votes'       => $voted
        ]);

    }

    /**
     * @Route("/nonVotedConf", name="nonVotedConferences")
     */
    public function nonVotedConf(VoteRepository $voteRepository,ConferenceRepository $conferenceRepository)
    {
        $votes = $voteRepository->findBy(['user' => $this->getUser()->getId()]);


        $conferenceIds = [];
        foreach ($votes as $vote) {

            $conferenceIds[] = $vote->getConference()->getId();
        }
        //dd($conferenceIds);
        $conferences = $conferenceRepository->findNonVotedConf($conferenceIds);
        //dd($conferenceRepository->findNonVotedConf($conferenceIds));
        $averageNote = [];
        foreach ($conferences as $conference) {
            $values = [];
            $votes = $conference->getVotes();
            foreach ($votes as $vote) {
                $values[] = $vote->getValue();
            }
            if (!empty($values))//check if $values is empty to evoid dividing by 0
            {
                //dd($values);
                $average = array_sum($values) / count($values);
                $averageNote[$conference->getId()] = $average;
            } else {
                $averageNote[$conference->getId()] = "pas encore de note";
            }
        }
            return $this->render('conference/nonVotedConf.html.twig', [
                'average' => $averageNote,
                'conferences' => $conferences
            ]);


    }

}

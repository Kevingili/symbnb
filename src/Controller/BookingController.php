<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    /**
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     */
    public function book(Ad $ad, Request $request, ObjectManager $manager)
    {
        $booking = new Booking;
        $form = $this->createForm(BookingType::class, $booking, [
            'validation_groups' => [ 'front']
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $this->getUser();
            $booking->setBooker($user);
            $booking->setAd($ad);

            //Si les dates ne sont pas disponibles message d'erreur
            if(!$booking->isBookableDates()){
                $this->addFlash(
                  "warning",
                  "Les dates que vous avez choisis ne peuvent être réservées: elles sont déjà prise"
                );
            } else {
                //Sinon enregistrement
                $manager->persist($booking);
                $manager->flush();

                return $this->redirectToRoute('booking_show', ["id" => $booking->getId(), "withAlert" => true]);
            }
        }
        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet d'afficher la page d'une réservation
     * @Route("/booking/{id}", name="booking_show")
     * @param Booking $booking
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function show(Booking $booking, Request $request, ObjectManager $manager){
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setAd($booking->getAd())
                    ->setAuthor($this->getUser())
            ;
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash(
                'success',
                "Votre commentaire a bien été pris en compte !"
            );
        }
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }
}

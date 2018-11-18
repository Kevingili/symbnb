<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $adminRole = new Role();
        $adminRole->setTitle("ROLE_ADMIN");
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName("Kevin");
        $adminUser->setLastName("GILIBERT");
        $adminUser->setEmail("kevin@mail.fr");
        $adminUser->setHash($this->encoder->encodePassword($adminUser, "password"));
        $adminUser->setPicture('https://avatars.io/twitter/Gilizoo');
        $adminUser->setIntroduction($faker->sentence());
        $adminUser->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>');
        $adminUser->addUserRole($adminRole);

        $manager->persist($adminUser);


        //Nous gerons les utilisateurs
        $users =[];
        $genres = ['male', 'female'];

        for($i=1; $i <= 10; $i++){
            $user = new User();

            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99) . '.jpg';


            $picture .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstName($genre));
            $user->setLastName($faker->lastName($genre));
            $user->setEmail($faker->email);
            $user->setIntroduction($faker->sentence());
            $user->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>');
            $user->setHash($hash);
            $user->setPicture($picture);

            $manager->persist($user);
            $users[] = $user;
        }

        //Nous gerons les annonces
        for ($i = 1; $i <=30; $i++){
            $ad = new Ad();

            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000,350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';


            $user = $users[mt_rand(0, count($users) -1)];

            $ad->setTitle($title);
            $ad->setCoverImage($coverImage);
            $ad->setIntroduction($introduction);
            $ad->setContent($content);
            $ad->setPrice(mt_rand(40,200));
            $ad->setRooms(mt_rand(1,5));
            $ad->setAuthor($user);

            for($j = 1; $j <= mt_rand(2,5); $j++){
                $image = new Image();
                $image->setUrl($faker->imageUrl());
                $image->setCaption($faker->sentence());
                $image->setAd($ad);

                $manager->persist($image);
            }

            //Gestion des réservations
            for($j =1; $j<= mt_rand(0,10); $j++){
                $booking = new Booking();
                $createdAt = $faker->dateTimeBetween('-6 months');
                $startDate = $faker->dateTimeBetween('-3 months');
                //Gestion de la date de fin
                $duration = mt_rand(3, 10);
                $endDate = (clone $startDate)->modify("+$duration days");

                $amount = $ad->getPrice() * $duration;

                $booker = $users[mt_rand(0, count($users) - 1)];

                $comment = $faker->paragraph();

                $booking->setBooker($booker);
                $booking->setAd($ad);
                $booking->setStartDate($startDate);
                $booking->setEndDate($endDate);
                $booking->setCreatedAt($createdAt);
                $booking->setAmount($amount);
                $booking->setComment($comment);

                $manager->persist($booking);

                //Gestion des commentaires
                if(mt_rand(0,1)){
                    $comment = new Comment();
                    $comment->setContent($faker->paragraph());
                    $comment->setRating(mt_rand(1,5));
                    $comment->setAuthor($booker);
                    $comment->setAd($ad);
                    $manager->persist($comment);
                }

            }

            $manager->persist($ad);
        }
        


        $manager->flush();
    }
}

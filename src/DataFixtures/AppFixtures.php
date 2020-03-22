<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Image;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
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
        $faker = \Faker\Factory::create('fr_FR');

        //Nous gérons les utilisateurs :

        $users = [];
        $genres = ['male','female'];
        for ($k = 1; $k <= 10; $k++){
            $user = new User();

            $genre = $faker->randomElement($genres);

           $picture = 'https://randomuser.me/api/portraits/';
           $pictureId = $faker->numberBetween(1,99) . '.jpg';

           if ($genre == 'male'){
               $picture = $picture . 'men/' . $pictureId;
           }else{
               $picture = $picture . 'women/' . $pictureId;
           }

           $hash = $this->encoder->encodePassword($user,'password');

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName)
                ->setPicture($picture)
                ->setEmail($faker->email)
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>')
                ->setHash($hash);

            $manager->persist($user);
            $users[] = $user;

        }
        //Nous gérons les annonces :

        for ($i = 1; $i <= 30; $i++){

            $ad = new Ad();

            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000,350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';

            $user = $users[mt_rand(0,count($users) - 1)];

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40,200))
                ->setRooms(mt_rand(1,5))
                ->setAuthor($user)
            ;

            for ($j = 1; $j<= mt_rand(2,5); $j++){

                $image = new Image();

                $image->setUrl($faker->imageUrl())
                      ->setCaption($faker->sentence())
                      ->setAd($ad);

                $manager->persist($image);
            }

            $manager->persist($ad);
        }
        $manager->flush();
    }
}

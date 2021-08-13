<?php declare(strict_types=1);

use GeekBrains\Blog\Comment;
use GeekBrains\Blog\Name;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\User;

require_once __DIR__ . '/vendor/autoload.php';

$faker = Faker\Factory::create('ru_RU');

if ($argv[1] === 'user') {
    echo new User(123, new Name($faker->firstName, $faker->lastName));
}

if ($argv[1] === 'post') {
    echo new Post(123, 234, $faker->sentence, $faker->text);
}

if ($argv[1] === 'comment') {
    echo new Comment(123, 234, 345, $faker->sentence);
}


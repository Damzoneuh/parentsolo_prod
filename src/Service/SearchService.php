<?php


namespace App\Service;


use App\Entity\Hobbies;
use App\Entity\Langages;
use App\Entity\User;

class SearchService
{
    public static function MatchingFilter(array $users, User $currentUser){
        $lookingFor = self::filterLookingFor($users, $currentUser);
        if (count($lookingFor) <= 10){
            $result = [];
            foreach ($users as $user){
                if ($user->getId() != $currentUser->getId()){
                    array_push($result, $user);
                }
            }
            return $result;
        }

        $relationship = self::filterRelationship($lookingFor, $currentUser);

        if (count($relationship) < 10){
            return self::mixArray($lookingFor, $relationship);
        }

        $hobbies = [];

        if ($currentUser->getProfil()->getHobbies()->count() > 0){
            $hobbies = self::filterHobbies($relationship, $currentUser);
        }

        if (count($hobbies) < 10){
            return self::mixArray($relationship, $hobbies);
        }

        $smoke = self::filterSmoke($hobbies, $currentUser);

        if (count($smoke) < 10) {
            return self::mixArray($hobbies, $smoke);
        }

        $religion = self::filterReligion($smoke, $currentUser);

        if (count($religion) < 10){
            return self::mixArray($smoke, $religion);
        }

        $lang = self::filterLang($religion, $currentUser);

        if (count($lang) < 10){
            return self::mixArray($religion, $lang);
        }

        $studies = self::filterStudy($lang, $currentUser);

        if (count($studies) < 10){
            return self::mixArray($lang, $studies);
        }

        $canton = self::filterCanton($studies, $currentUser);

        if (count($canton) < 10){
            return self::mixArray($studies, $canton);
        }

        return $canton;
    }

    public static function filterAge(User $user, $minAge, $maxAge){
        $date = new \DateTime('now');
        $birthDate = $user->getBirthdate();
        $age = $date->diff($birthDate);
        if ($age->y > $minAge && $age->y < $maxAge || $age->y == $minAge || $age->y == $maxAge){
            return true;
        }
        return false;
    }

    public static function filterLookingFor(array $users, User $currentUser){
        $result = [];
        /** @var User $user */
        foreach ($users as $user){
            if (null === $currentUser->getIsLookingSex()){
                array_push($result, $user);
            }
            switch ($currentUser->getIsLookingSex()){
                case null:
                    array_push($result, $user);
                    break;

                case true:
                case false:
                    if ($currentUser->getIsLookingSex() === $user->getProfil()->getIsMan()
                        || null === $currentUser->getIsLookingSex()){
                        array_push($result, $user);
                    }
                    break;
            }
        }
        return $result;
    }

    public static function filterRelationship(array $users, User $currentUser){
        $result = [];
        /** @var User $user */
        foreach ($users as $user){
            //dump($user->getProfil()); die();
            if (null !== $user->getProfil()->getRelation()
                && $currentUser->getProfil()->getRelation() === $user->getProfil()->getRelation()){
                array_push($result, $user);
            }
        }

        return $result;
    }

    public static function filterHobbies(array $users, User $currentUser){
        $result = [];
        /** @var User $user */
        $currentUserHobbies = [];
        /** @var Hobbies $targetHobby */
        foreach ($currentUser->getProfil()->getHobbies()->getValues() as $targetHobby){
            array_push($currentUserHobbies, $targetHobby->getId());
        }
        foreach ($users as $user){
            if ($user->getProfil()->getHobbies()->count() > 0){
                /** @var Hobbies $hobby */
                foreach ($user->getProfil()->getHobbies()->getValues() as $hobby){
                    if (in_array($hobby->getId(), $currentUserHobbies) && !in_array($user->getId(), $result)){
                        array_push($result, $user);
                    }
                }
            }
        }

        return $result;
    }

    public static function filterSmoke(array $users, User $currentUser){
        $result = [];

        if (null !== $currentUser->getProfil()->getSmoke()){
            /** @var User $user */
            foreach ($users as $user){
                if (null !== $user->getProfil()->getSmoke()
                    && $currentUser->getProfil()->getSmoke()->getId() === $user->getProfil()->getSmoke()->getId()){
                    array_push($result, $user);
                }
            }
        }
        else{
            foreach ($users as $user){
                array_push($user->getId());
            }
        }

        return $result;
    }

    public static function filterReligion(array $users, User $currentUser){
        $result = [];

        if (null !== $currentUser->getProfil()->getReligion()){
            /** @var User $user */
            foreach ($users as $user){
                if ($currentUser->getProfil()->getReligion() === $user->getProfil()->getReligion()){
                    array_push($result, $user);
                }
            }
        }

        else{
            foreach ($users as $user){
                array_push($result, $user);
            }
        }

        return $result;
    }


    public static function filterLang(array $users, User $currentUser){
        $result = [];
        if ($currentUser->getProfil()->getLangages()->count() === 0){
            return $users;
        }
        $targetLang = [];
        /** @var Langages $userLang */
        foreach ($currentUser->getProfil()->getLangages()->getValues() as $userLang){
            array_push($targetLang, $userLang->getId());
        }
        /** @var User $user */
        foreach ($users as $user){
            if ($user->getProfil()->getLangages()->count() > 0) {
                /** @var Langages $lang */
                foreach ($user->getProfil()->getLangages()->getValues() as $lang){
                    if (in_array($lang->getId(), $targetLang)){
                        array_push($result, $user);
                    }
                }
            }
        }

        return $result;
    }

    public static function filterStudy(array $users, User $currentUser){
        if (null === $currentUser->getProfil()->getStudies()){
            return $users;
        }
        $result = [];
        /** @var User $user */
        foreach ($users as $user){
            if ($user->getProfil()->getStudies()->getId() === $currentUser->getProfil()->getStudies()->getId()){
                array_push($result, $user);
            }
        }

        return $result;
    }

    public static function filterCanton(array $users, User $currentUser){
        $result = [];
        /** @var User $user */
        foreach ($users as $user){
            if ($user->getProfil()->getCity()->getCanton()->getId() === $currentUser->getProfil()->getCity()->getCanton()->getId()){
                array_push($result, $user);
            }
        }

        return $result;
    }

    private static function mixArray(array $previous, array $next) : array {
        $mixed = [];
        if (count($next) > 0){
            foreach ($previous as $row){
                if (!in_array($row, $next)){
                    array_push($mixed, $row);
                }
            }
            return $mixed;
        }
        return $previous;
    }
}
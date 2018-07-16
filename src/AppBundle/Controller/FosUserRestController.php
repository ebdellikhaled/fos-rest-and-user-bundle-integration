<?php

namespace AppBundle\Controller;

use AppBundle\Entity\UserSkills;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @package AppBundle\Controller
 * @RouteResource("Users", pluralize=false)
 */
class FosUserRestController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @Annotations\Get("/users")
     * @return array
     * @Annotations\View(serializerGroups={
     *   "users_all"
     * })
     *
     * Note: Could be refactored to make use of the User Resolver in Symfony 3.2 onwards
     * more at : http://symfony.com/blog/new-in-symfony-3-2-user-value-resolver-for-controllers
     */
    public function getUsersAction()
    {

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();

        if ($users === null) {
            return new View("there are no users exist", Response::HTTP_NOT_FOUND);
        }
        return $users;
    }


    /**
     * @Annotations\Get("/users/{id}")
     * @param int $id
     * @return \AppBundle\Entity\User|View|object
     */
    public function getByIdAction(int $id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if ($user === null) {
            return new View("there are no user exist", Response::HTTP_NOT_FOUND);
        }
        return $user;
    }

    /**
     * @Annotations\Get("/users/{id}/projects")
     * @param int $id
     * @return \AppBundle\Entity\User|View|object
     */
    public function getProjectsByIdAction(int $id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if ($user === null) {
            return new View("there are no user exist", Response::HTTP_NOT_FOUND);
        }
        return $user->getProjects();
    }

    /**
     * @Annotations\Post("/users/{id}/enable")
     * @param int $id
     * @return \AppBundle\Entity\User|View|object
     */
    public function setEnabledAction(int $id)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        if ($user === null) {
            return new View("there are no user exist", Response::HTTP_NOT_FOUND);
        }
        $userManager = $this->get('fos_user.user_manager');
        $user->setEnabled(!$user->isEnabled());
        $userManager->updateUser($user);

        return new View('user access successfully updated', Response::HTTP_NO_CONTENT);
    }


    /**
     * @Annotations\Post("/users/skill")
     * @param Request $request
     * @return \AppBundle\Entity\User|View|object
     */
    public function addSkillAction(Request $request)
    {
        $skills = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();

        foreach ($skills as $key => $value) {
            $skill = $this->getDoctrine()->getRepository('AppBundle:Skill')->findOneBy(["title" => $value['text']]);
            $this->getUser()->removeSkills($skill);
            $em->persist($skill);
        }
        foreach ($skills as $key => $value) {
            $skill = $this->getDoctrine()->getRepository('AppBundle:Skill')->findOneBy(["title" => $value['text']]);
            $userSkill = new UserSkills(0, $this->getUser(), $skill);
            $em->persist($userSkill);
        }

        $em->flush();
        return new View($skills, Response::HTTP_CREATED);
    }


    /**
     * @Annotations\Get("/users/skills/{id}")
     * @param int $id
     * @return \AppBundle\Entity\User|View|object
     */
    public function getUserSkillsAction($id)
    {
        return $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id)->getSkills();
    }

    /**
     * @Annotations\Get("/students")
     * @return array
     * @Annotations\View(serializerGroups={
     *   "users_all"
     * })
     */
    public function getStudentsAction()
    {
        return $user = $this->getDoctrine()->getRepository('AppBundle:UserSkills')->findAll();
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: digitus
 * Date: 12/9/17
 * Time: 10:40 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use AppBundle\Entity\Skill;
use AppBundle\Entity\User;
use AppBundle\Entity\UserSkills;
use AppBundle\Form\Type\ProjectType;
use AppBundle\Form\Type\SkillType;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;

/**
 * Class SkillController
 * @package AppBundle\Controller
 */
class SkillController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets an individual Skill
     *
     * @param int $id
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @ApiDoc(
     *     output="AppBundle\Entity\Skill",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function getAction(int $id)
    {
        return $this->getDoctrine()->getRepository('AppBundle:Skill')->find($id);
    }


    /**
     * Gets an individual Skill
     *
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @QueryParam(name="title", nullable=true)
     * @QueryParam(name="state", nullable=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @ApiDoc(
     *     output="AppBundle\Entity\Skill",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function cgetAction(ParamFetcher $paramFetcher)
    {

        foreach ($paramFetcher->all() as $name => $value) {
            if ($name === "state") {
                $state = $value;
            }

        }
        foreach ($paramFetcher->all() as $name => $value) {
            if ($name === "title") {
                $title = $value;
                $skills = $this->getDoctrine()->getRepository("AppBundle:Skill")->createQueryBuilder('s')
                    ->andWhere('s.title LIKE :title')
                    ->andWhere('s.state = :state')
                    ->setParameters(array('title' => '%' . $title . '%', 'state' => $state))
                    ->getQuery()
                    ->getResult();
                return new View($skills, Response::HTTP_OK);
            }
        }

    }

    /**
     * @param Request $request
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\SkillType",
     *     output="AppBundle\Entity\Skill",
     *     statusCodes={
     *         201 = "Returned when a new Skill has been successful created",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function postAction(Request $request)
    {
        $skill = new Skill();
        $form = $this->createForm(SkillType::class, $skill, [
            'csrf_protection' => false,
        ]);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }
        /**
         * @var $skill Skill
         */
        $skillText = $request->get('title');
        $skills = explode(",", $skillText);

        $em = $this->getDoctrine()->getManager();
        foreach ($skills as $value) {
            $skill = new Skill();
            $skill->setTitle("#" . $value);
            $skill->setState(false);
            $skill->setUser($this->getUser());
            $em->persist($skill);
        }

        $em->flush();
        return new View('skill successfully created', Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\SkillType",
     *     output="AppBundle\Entity\Skill",
     *     statusCodes={
     *         204 = "Returned when an existing Skill has been successful updated",
     *         400 = "Return when errors",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function putAction(Request $request, int $id)
    {
        /**
         * @var $skill Skill
         */
        $skill = $this->getDoctrine()->getRepository('AppBundle:Skill')->find($id);
        if ($skill === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }


        $em = $this->getDoctrine()->getManager();
        $skill->setState(!$skill->getState());
        $em->persist($skill);
        $em->flush();
        return new View('skill successfully updated', Response::HTTP_NO_CONTENT);
    }


    /**
     * @param int $id
     * @return View
     *
     * @ApiDoc(
     *     statusCodes={
     *         204 = "Returned when an existing Skill has been successful deleted",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function deleteAction(int $id)
    {
        /**
         * @var $skill Skill
         */
        $skill = $this->getDoctrine()->getRepository('AppBundle:Skill')->find($id);
        if ($skill === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($skill);
        $em->flush();

        return new View(null, Response::HTTP_NO_CONTENT);
    }
}
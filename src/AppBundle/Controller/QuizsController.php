<?php
/**
 * Created by PhpStorm.
 * User: digitus
 * Date: 12/9/17
 * Time: 10:40 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\CoursQuiz;
use AppBundle\Entity\Project;
use AppBundle\Entity\Quiz;
use AppBundle\Entity\Skill;
use AppBundle\Entity\User;
use AppBundle\Entity\UserSkills;
use AppBundle\Form\Type\ProjectType;
use AppBundle\Form\Type\QuizType;
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
 * Class QuizController
 * @package AppBundle\Controller
 */
class QuizsController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Gets an individual Quiz
     *
     * @Annotations\Get("/quizs/{id}")
     * @param int $id
     * @return \AppBundle\Entity\Quiz|View|object
     *
     * @ApiDoc(
     *     output="AppBundle\Entity\Quiz",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function getAction(int $id)
    {
        // $cours = $this->getDoctrine()->getRepository('AppBundle:Cours')->find($id);
        return $this->getDoctrine()->getRepository('AppBundle:Quiz')->findBy(['cours' => $id]);
    }


    /**
     * Gets an individual Quiz
     *
     * @param Request $request
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @ApiDoc(
     *     output="AppBundle\Entity\Quiz",
     *     statusCodes={
     *         200 = "Returned when successful",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function cgetAction(Request $request)
    {

        return $this->getDoctrine()->getRepository('AppBundle:Quiz')->findAll();

    }

    /**
     * @param Request $request
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\QuizType",
     *     output="AppBundle\Entity\Quiz",
     *     statusCodes={
     *         201 = "Returned when a new Quiz has been successful created",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function postAction(Request $request)
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz, [
            'csrf_protection' => false,
        ]);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }
        /**
         * @var $skill Skill
         */
        $quiz = $form->getData();

        $em = $this->getDoctrine()->getManager();
        $cours = $this->getDoctrine()->getRepository('AppBundle:Cours')->find($request->get('coursId'));
        $quiz->setCours($cours);
        $em->persist($quiz);
        $em->flush();
        return new View('quiz successfully created', Response::HTTP_CREATED);
    }


    /**
     * @Annotations\Post("/quizs/rep")
     */
    public function repAction(Request $request)
    {
        $rep = (array)$request->get('responses');
        $em = $this->getDoctrine()->getManager();
        $cours = $this->getDoctrine()->getRepository('AppBundle:Cours')->find($request->get('coursId'));
        $coursSkills = $this->getDoctrine()->getRepository("AppBundle:CoursSkills")->createQueryBuilder('s')
            ->andWhere('s.cours = :cour')
            ->setParameters(array('cour' => $cours))
            ->getQuery()
            ->getArrayResult();
        $count = 0;
        foreach ($rep as $key => $value) {
            $quiz = $this->getDoctrine()->getRepository('AppBundle:Quiz')->find($value["quiz"]);
            if ($value["response"] == $quiz->getResponse())
                $count++;
        }
        if ($count == count($rep)) {
            $coursQuiz = new CoursQuiz();
            $coursQuiz->setOk(true);
            $coursQuiz->setUserId($this->getUser()->getId());
            $coursQuiz->setCours($cours);
            $coursQuiz->setChoiceSelected('height');
            foreach ($coursSkills as $c) {
                $userS = $this->getDoctrine()->getRepository("AppBundle:UserSkills")->findOneBy(['users' => $this->getUser(), 'skills' => $c["skill_id"]]);
                $s= $this->getDoctrine()->getRepository("AppBundle:Skill")->find($c["skill_id"]);
                if ($userS) {
                    $userS->setScore($userS->getScore() + 3);
                } else {
                    $userS = new UserSkills(3, $this->getUser(), $s);
                }
            }
        } else if ($count > count($rep) / 2) {
            $coursQuiz = new CoursQuiz();
            $coursQuiz->setOk(true);
            $coursQuiz->setUserId($this->getUser()->getId());
            $coursQuiz->setCours($cours);
            $coursQuiz->setChoiceSelected('medium');
            foreach ($coursSkills as $c) {
                $userS = $this->getDoctrine()->getRepository("AppBundle:UserSkills")->findOneBy(['users' => $this->getUser(), 'skills' => $c["skill_id"]]);
                $s= $this->getDoctrine()->getRepository("AppBundle:Skill")->find($c["skill_id"]);
                if ($userS) {
                    $userS->setScore($userS->getScore() + 2);
                } else {
                    $userS = new UserSkills(2, $this->getUser(), $s);
                }
            }
        } else if ($count < count($rep) / 2) {
            $coursQuiz = new CoursQuiz();
            $coursQuiz->setOk(true);
            $coursQuiz->setUserId($this->getUser()->getId());
            $coursQuiz->setCours($cours);
            $coursQuiz->setChoiceSelected('basic');
            foreach ($coursSkills as $c) {
                $userS = $this->getDoctrine()->getRepository("AppBundle:UserSkills")->findOneBy(['users' => $this->getUser(), 'skills' => $c["skill_id"]]);
                $s= $this->getDoctrine()->getRepository("AppBundle:Skill")->find($c["skill_id"]);
                if ($userS) {
                    $userS->setScore($userS->getScore() + 1);
                } else {
                    $userS = new UserSkills(1, $this->getUser(), $s);
                }
            }
        } else if ($count == 0) {
            $coursQuiz = new CoursQuiz();
            $coursQuiz->setOk(false);
            $coursQuiz->setUserId($this->getUser()->getId());
            $coursQuiz->setCours($cours);
            $coursQuiz->setChoiceSelected('null');
        }
        $em->persist($userS);
        $em->persist($coursQuiz);
        $em->flush();
        return $coursQuiz->getChoiceSelected();
    }

    /**
     * @param Request $request
     * @param int $id
     * @return View|\Symfony\Component\Form\Form
     *
     * @ApiDoc(
     *     input="AppBundle\Form\Type\QuizType",
     *     output="AppBundle\Entity\Quiz",
     *     statusCodes={
     *         204 = "Returned when an existing Quiz has been successful updated",
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
        $quiz = $this->getDoctrine()->getRepository('AppBundle:Quiz')->find($id);
        if ($quiz === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }


        $em = $this->getDoctrine()->getManager();
        $quiz->setResponse(!$quiz->getResponse());
        $em->persist($quiz);
        $em->flush();
        return new View('quiz successfully updated', Response::HTTP_NO_CONTENT);
    }


    /**
     * @param int $id
     * @return View
     *
     * @ApiDoc(
     *     statusCodes={
     *         204 = "Returned when an existing Quiz has been successful deleted",
     *         404 = "Return when not found"
     *     }
     * )
     */
    public function deleteAction(int $id)
    {
        /**
         * @var $quiz Quiz
         */
        $quiz = $this->getDoctrine()->getRepository('AppBundle:Quiz')->find($id);
        if ($quiz === null) {
            return new View(null, Response::HTTP_NOT_FOUND);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($quiz);
        $em->flush();

        return new View(null, Response::HTTP_NO_CONTENT);
    }
}
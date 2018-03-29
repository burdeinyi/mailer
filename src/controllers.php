<?php

/**
 * @var \Silex\Application $app
 */

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;

$app->before(function (Request $request) {
    if (strpos($request->headers->get('Content-Type'), 'application/json') === 0) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : []);
    }
}
);

$app->error(function (Exception $exception) {
    return new Response(null, 400);
});

$app->post('/sendmail', function (Request $request) use ($app) {
    $transport = new Swift_SendmailTransport('/usr/sbin/sendmail -bs');
    $mailer = new Swift_Mailer($transport);

    /** @var \Symfony\Component\Form\Form $form */
    $form = $app['form.factory']->createBuilder(FormType::class)
        ->add('subject', TextType::class, ['constraints' => [new Assert\NotBlank()]])
        ->add('to', TextType::class, ['constraints' => [new Assert\NotBlank(), new Assert\Email()]])
        ->add('cc', CollectionType::class, [
            'entry_type' => TextType::class,
            'allow_add' => true,
            'by_reference' => false,
            'label' => false,
            'required' => false
        ])
        ->add('from', TextType::class, ['constraints' => [new Assert\NotBlank(), new Assert\Email()]])
        ->add('body', TextType::class, ['constraints' => [new Assert\NotBlank()]])
        ->getForm();

    $form->submit($request->request->all());

    if (!$form->isValid()) {
        $app['logger']->error((string)$form->getErrors(true, false));
        throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException();
    }

    $data = $form->getData();

    $data['to'] = $app['email_config']['fake_mode'] ? $app['email_config']['fake_email'] : $data['to'];

    $message = (new Swift_Message($data['subject']))
        ->setFrom($data['from'])
        ->setTo($data['to'])
        ->setBody($data['body'], 'text/html');

    if ($app['email_config']['fake_mode'] == false &&
        array_key_exists('cc', $data) &&
        is_array($data['cc']) && !empty($data['cc'])
    ) {
        $message->setCc($data['cc']);
    }

    $app['logger']->info($data['to'], ['MESSAGE TO']);
    $app['logger']->info($message->toString(), ['MESSAGE INFO']);

    if ($app['email_config']['ghost_mode']) {
        $app['logger']->info(1, ['NUM EMAILS SENT']);
        return new Response();
    }

    $result = $mailer->send($message);

    $app['logger']->info($result, ['NUM EMAILS SENT']);

    if ($result === 0) {
        throw new \Swift_TransportException('0 successful recipients');
    }

    return new Response();
});

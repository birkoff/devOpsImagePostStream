<?php


namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class NotificationController extends Controller
{
    /**
     * Matches /api/notification/send exactly
     * @Route("/api/notification/send")
     * @param Request $request
     * @return JsonResponse
     */
    public function send(Request $request)
    {
        $message = $request->request->get('message');
        $subject = $request->request->get('subject');

        $notificationService = $this->get('app.notification_service');

        try {
            $messageId = $notificationService->send($message, $subject);
        } catch(\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        return $this->json(['status' => 'in_process MessageId: ' . $messageId]);
    }
}
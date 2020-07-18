<?php

namespace App\Controller;

use App\Model\Api\GoogleApi;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\Voice;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function index(ParameterBagInterface $bag, Request $request)
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    public function tts(ParameterBagInterface $bag, Request $request)
    {
        $tts = new GoogleApi($bag);
        if ($text = $request->get('text-to-speech')) {
            $tts->execute($text);
        } else {
            $tts->execute('Скажи что-то сука!');
        }
        return $this->render('default/index.html.twig');
    }
}

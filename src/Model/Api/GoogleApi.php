<?php


namespace App\Model\Api;


use Google\ApiCore\ApiException;
use Google\ApiCore\ValidationException;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GoogleApi
{
    private $textToSpeechClient;

    private $bag;

    /**
     * GoogleApi constructor.
     * @param ParameterBagInterface $bag
     * @throws ValidationException
     */
    public function __construct(ParameterBagInterface $bag)
    {
        $this->bag = $bag;
        $this->textToSpeechClient = new TextToSpeechClient([
            'credentials' => json_decode(file_get_contents($bag->get('kernel.project_dir').'/config/google_api.json'), true)
        ]);
    }

    /**
     * @param $text
     * @return SynthesisInput
     */
    public function setText($text)
    {
        return (new SynthesisInput())
            ->setText($text);
    }

    /**
     * @param string $code
     * @param string $name
     * @param int $gender
     * @return VoiceSelectionParams
     */
    public function setVoice($code, $name, $gender = SsmlVoiceGender::MALE)
    {
        return (new VoiceSelectionParams())
            ->setLanguageCode($code)
            ->setName($name)
            ->setSsmlGender($gender);
    }

    /**
     * @param int $encoding
     * @return AudioConfig
     */
    public function setAudio($encoding)
    {
        return (new AudioConfig())
                ->setAudioEncoding($encoding)
            ;
    }

    /**
     * @param string $text
     * @param string $voiceCode
     * @param string $voiceName
     * @param null $audio
     * @throws ApiException
     */
    public function execute($text, $voiceCode  = 'ru-RU', $voiceName = 'ru-RU-Standard-D', $audio = AudioEncoding::MP3)
    {
        try {
            $response = $this->textToSpeechClient->synthesizeSpeech($this->setText($text), $this->setVoice($voiceCode, $voiceName), $this->setAudio($audio));
            $audioContent = $response->getAudioContent();
        } finally {
            $response->clear();
        }
        file_put_contents($this->bag->get('kernel.project_dir').'/public/output.mp3', $audioContent);
    }

}
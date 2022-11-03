<?php
require 'vendor/autoload.php';

use Clarifai\ClarifaiClient;
use Clarifai\Api\Data;
use Clarifai\Api\Image;
use Clarifai\Api\Input;
use Clarifai\Api\PostModelOutputsRequest;
use Clarifai\Api\Status\StatusCode;

class Clarifai
{
    private $client = null;
    private $metadata = null;

    /**
     * *This constructor will initialize your Clarifai client, don't forget to get your own Personal Access Token (PAT).
     * @param string $pat
     * @param string $metadata
     * @return void
     *
     */
    public function __construct()
    {
        $this->client = ClarifaiClient::grpc();
        $this->metadata = ['Authorization' => ['Key {use ur own PAT key}']];
    }

    /**
     * *This is the main function of predictor of image, you can use this function to predict the image.
     * @return ClarifaiClient
     *
     */
    public function predict()
    {
        $input = new Input([
            'data' => new Data([
                'image' => new Image([
                    'url' => 'https://samples.clarifai.com/dog2.jpeg'
                ])
            ])
        ]);

        $request = new PostModelOutputsRequest([
            'user_app_id' => (object)array("user_id" => '{your own user_id}',"app_id" => "{your own app_id}"),
            'model_id' => 'aaa03c23b3724a16a56b629203edc62c',
            'inputs' => [$input]
        ]);

        [$response, $status] = $this->client->PostModelOutputs(
            $request,
            $this->metadata
        )->wait();

        if ($status->code !== 0) {
            throw new Exception("Error: {$status->details}");
        }
        if ($response->getStatus()->getCode() != StatusCode::SUCCESS) {
            throw new Exception("Failure response: " . $response->getStatus()->getDescription() . " " .
                $response->getStatus()->getDetails());
        }

        echo "Predicted concepts:\n";
        foreach ($response->getOutputs()[0]->getData()->getConcepts() as $concept) {
            echo $concept->getName() . ": " . number_format($concept->getValue(), 2) . "\n";
        }
    }
}

$x = new Clarifai();
echo $x->predict();

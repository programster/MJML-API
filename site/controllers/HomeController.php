<?php


class HomeController extends AbstractSlimController
{
    public static function registerRoutes($app)
    {
        $app->get('/', function (Psr\Http\Message\ServerRequestInterface $request, Psr\Http\Message\ResponseInterface $response) {
            $body = $response->getBody();
            $body->write('Welcome to Programster\'s MJML API. POST an upload file to /v1/render.'); // returns number of bytes written
            $newResponse = $response->withBody($body);
            return $newResponse;
        });

        $app->post('/v1/render', function (Psr\Http\Message\ServerRequestInterface $request, Psr\Http\Message\ResponseInterface $response, $args) {
            $controller = new HomeController($request, $response, $args);
            return $controller->handleRenderRequest();
        });
    }


    private function handleRenderRequest() : \Psr\Http\Message\ResponseInterface
    {
        try {
            $uploadManager = new \Programster\UploadFileManager\UploadFileManager();
            $uploads = $uploadManager->getUploadFiles();

            if (count($uploads) !== 1)
            {
                throw new Exception("You need to upload a single MJML file.");
            }

            /* @var $file \Programster\UploadFileManager\UploadFile */
            $file = array_pop($uploads);

            if ($file->wasSuccessful() === false)
            {
                throw new Exception("There was an issue with the uploading of the file.");
            }

            $content = file_get_contents($file->getFilepath());

            $command = __DIR__ . "/node_modules/.bin/mjml --validate \"{$file->getFilepath()}\"";
            $output = shell_exec($command);

            if (str_contains($output, "Validation failed"))
            {
                $response = new \Slim\Psr7\Response(400);
                $body = $response->getBody();

                $responseData = [
                    'error' => [
                        'message' => 'validation failed',
                        'mjml_output' => $output,
                    ]
                ];

                $body->write(json_encode($responseData, JSON_UNESCAPED_SLASHES));
                $response = $response->withBody($body);
            }
            else
            {
                $tempName = tempnam(sys_get_temp_dir(), "upload-file-");
                move_uploaded_file($file->getFilepath(), $tempName);
                $command2 = __DIR__ . "/../node_modules/.bin/mjml $tempName --stdout";
                $output = shell_exec($command2);
                $response = new \Slim\Psr7\Response();
                $body = $response->getBody();
                $body->write($output);
                $response->withHeader('Content-Type', 'text/plain');
            }
        }
        catch (Exception $e)
        {
            $response = new \Slim\Psr7\Response(400);
            $body = $response->getBody();

            $responseData = [
                'error' => [
                    'message' => $e->getMessage(),
                ]
            ];

            $body->write(json_encode($responseData, JSON_UNESCAPED_SLASHES));
            $response = $response->withBody($body);
        }

        return $response;
    }
}

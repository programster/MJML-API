This is my own version of the MJML API server, to resolve escaping frustrations
I was having with other APIs. With this API, one POSTs an uploaded file to the
`/v1/render` endpoint, and the API will return the HTML version of the file.
This way, we do not have to worry about any escaping or JSON encoding issues
with the submission of the MJML content.

## Example Usage
You can run the API by running the following command:

```bash
docker run -d \
  --restart=always \
  -p80:80 \
  programster/mjml-server
```

Alternatively, those who have installed Docker Compose can an use the example
`docker-compose.yml` file below:

```yaml
version: "3.9"

services:
  app:
    container_name: mjml-server
    image: programster/mjml-server
    restart: always
    ports:
      - "80:80"

```

... and then run:

```bash
docker-compose up -d
```

To convert a file, you can use curl like so (assuming you deployed locally)

```bash
curl -F my-file.mjml=@/path/to/file.mjml http://localhost/v1/render
```

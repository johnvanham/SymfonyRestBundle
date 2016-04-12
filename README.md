# Symfony REST Bundle

General methods to help with REST-ful approach in a Symfony 3 application.

Bundle features:

* handling API errors
* better handling of exceptions
* pagination and ordering functionality
* management CORS headers

## API error handling

* inspired by the [Heroku API error responses](https://devcenter.heroku.com/articles/platform-api-reference#errors)
* additional response data handling on top of [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle)
* better management of unauthorised user errors
* error data is returned in a format:
    ```
    {
        'id': 'status_code_id',
        'message': 'error message',
    }
    ```

**Example usage**

```
<?php

namespace My\UserApiBundle\Controller;

...
use LoftDigital\SymfonyRestBundle\Controller\RestController;

class UserController extends RestController
{
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isValid()) {
            ...

            return $this->statusCreated($user);
        }

        return $this->processFormValidationError($form->getErrors(true));
    }

    ...
}
```

## Pagination

* inspired by the [Range header, I choose you!](http://otac0n.com/blog/2012/11/21/range-header-i-choose-you.html)
* injects `order`, `max`, `offset`, `range` to a `Request` object based on a request data
* use `ListResponse` response format to automatically inject response headers
    * `Accept-Ranges`
    * `Content-Range`
    * `Next-Range`

**Example headers**

Request:
```
Range:email;order=desc,max=2
```

Response:
```
Accept-Ranges:id, email
Content-Range:email 1-2/6
Next-Range:email; max=2,offset=2
```

**Example usage**

```
<?php

namespace My\UserApiBundle\Handler;

...
use My\UserApiBundle\Repository\UserRepository;
use LoftDigital\SymfonyRestBundle\Handler\AbstractItemHandler;
use LoftDigital\SymfonyRestBundle\Model\ListResponse;

class UserHandler extends AbstractItemHandler
{
    /** @var UserRepository */
    protected $repository;

    public function getAcceptRanges()
    {
        return [self::RANGE_ID, self::RANGE_EMAIL];
    }

    public function getDefaultRange()
    {
        return self::RANGE_EMAIL;
    }

    public function getAll($max = 20, $offset = 0)
    {
        $repository = $this->repository
            ->setOrderBy('user.id')
            ->setOrder($this->order);
        $paginator = $repository->findAllUsers($max, $offset);

        return new ListResponse(
            $paginator,
            $offset,
            $this->range,
            $this->getAcceptRanges()
        );
    }
}
```

<?php /** @noinspection PhpStrFunctionsInspection */

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    protected $response = null;
    protected $username = null;
    protected $password = null;
    protected $client = null;
    protected $parameters = null;
    protected array $table;

    /**
     * Getters and Setters
     */


    /**
     * @return null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param null $response
     */
    public function setResponse($response): void
    {
        $this->response = $response;
    }

    /**
     * @return null
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param null $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param null $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return null
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param null $client
     */
    public function setClient($client): void
    {
        $this->client = $client;
    }

    /**
     * @return null
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param null $parameters
     */
    public function setParameters($parameters): void
    {
        $this->parameters = $parameters;
    }


    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct($github_username, $github_password)
    {
        $this->username = $github_username;
        $this->password = $github_password;
    }

    /**
     * @Given I am an anonymous user
     */
    public function iAmAnAnonymousUser(): bool
    {
        return true;
    }


    /**
     * @When I search for :arg1
     */
    public function iSearchFor($arg1)
    {
        $client = new GuzzleHttp\Client(['base_uri' => 'https://api.github.com']);
        $this->response = $client->get('/search/repositories?q=' . $arg1);
    }


    /**
     * @Then I expect a :arg1 response code
     * @throws Exception
     */
    public function iExpectAResponseCode($arg1)
    {
        $response_code = $this->response->getStatusCode();
        if ($response_code <> $arg1) {
            throw new Exception("It didn't work. We expected a 200 response but got a $arg1 response" . $response_code);
        }
    }

    /**
     * @throws Exception
     * @noinspection PhpStrFunctionsInspection
     */
    protected function iExpectASuccessfulRequest(){

         $response_code =  $this->response->getStatusCode();

        if (substr($response_code, 0, 1) == '2') {
            echo "Success with code: ", $response_code;
             } else {
            throw new Exception("We expected a successful request but received a $response_code instead!");
                 }
        }


    /**
     * @throws Exception
     */
    protected function iExpectAFailedRequest(){

        $response_code = $this->response->getStatusCode();

        if (substr($response_code, 0, 1) != '2') {
            throw new Exception("Request failed with response code: $response_code ");
        } else {
            echo "Success with code: ", $response_code;
        }

    }

    /**
     * @Then I expect at least :arg1 result
     * @throws Exception
     */
    public function iExpectAtLeastResult($arg1)
    {
        $data = $this->getBodyAsJson();
        if ($data['total_count'] < $arg1) {
            throw new Exception("We expected at least $arg1 results but found: " . $data['total_count']);
        }
    }

    /**
     * @Given I am an authenticated user
     * @throws Exception
     */
    public function iAmAnAuthenticatedUser()
    {
        $this->client = new GuzzleHttp\Client([
            'base_uri' => 'https://api.github.com',
            'auth' => [$this->username, $this->password]
        ]);
        $this->response = $this->client->get('/'); //Get to the root of the API
        $this->iExpectAResponseCode(200);

    }

    /**
     * @When I request a list of my repositories
     * @throws Exception
     */
    public function iRequestAListOfMyRepositories()
    {
        $this->response = $this->client->get('/user/repos');
        $this->iExpectAResponseCode(200);

    }

    /**
     * @Then The results should include a repository named :arg1
     * @throws Exception
     */
    public function theResultsShouldIncludeARepositoryNamed($arg1) : bool

    {


        $repositories = $this->getBodyAsJson();

        foreach ($repositories as $repository) {
            if ($repository['name'] == $arg1) {
                return true;
            }
        }

        throw new Exception("Expected to find a repository named '$arg1' but did not.");
    }

    protected function getBodyAsJson()
    {
        return json_decode($this->response->getBody(), true);
    }

    /**
     * @When I create the :arg1 repository
     * @throws Exception
     */
    public function iCreateTheRepository($arg1)
    {

        $parameters = json_encode(['name' => $arg1]);

         if(!$parameters == $this->iRequestAListOfMyRepositories()){
          $this->client->post('/user/repos', ['body' => $parameters]);
          $this->iExpectASuccessfulRequest();
         } else{
             throw new Exception("The repo $arg1 already exists.");
         }
    }

    /**
     * @Given I have a repository called :arg1
     * @throws Exception
     */
    public function iHaveARepositoryCalled($arg1)
    {
        $this->iRequestAListOfMyRepositories();
        $this->theResultsShouldIncludeARepositoryNamed($arg1);
    }

    /**
     * @When I watch the :arg1 repository
     */
    public function iWatchTheRepository($arg1)
    {
        $watch_url = '/repos/' . $this->username .'/' .$arg1 . '/subscription';
        $parameters = json_encode(['subscribed' => 'true']);

        $this->client->put($watch_url, ['body' => $parameters]);
    }

    /**
     * @Then The :arg1 repository will list me as a watcher
     * @throws Exception
     */
    public function theRepositoryWillListMeAsAWatcher($arg1): bool
    {
        $watch_url = '/repos/' . $this->username . '/' . $arg1 . '/subscribers';
        $this->response = $this->client->get($watch_url);

        $subscribers = $this->getBodyAsJson();

        foreach($subscribers as $subscriber){
            if($subscriber['login'] == $this->username){
                return true;
            }
        }
        throw new Exception("Did not find '{$this->username}' as a watcher as expected.");
    }

    /**
     * @Then I delete the repository called :arg1
     * @throws Exception
     */
    public function iDeleteTheRepositoryCalled($arg1)
    {
        $delete = '/repos/' . $this->username . '/' .$arg1;
        $this->response = $this->client->delete($delete);
        $this->iExpectAResponseCode(204);
    }

    /**
     * @Given I have the following repositories:
     * @throws Exception
     */
    public function iHaveTheFollowingRepositories(TableNode $table)
    {
        $this->table = $table->getRows(); //get a list of rows loaded in memory
        array_shift($this->table);//get rid of the first row. that is just the table header which we don't need

        foreach ($this->table as $id => $row){//get the contents of our table

            $this->table[$id] ['name'] = $row[0] . '/' .$row[1]; //GitHub refers to projects as owner/project name

          $this->response = $this->client->get('/repos/' . $row[0] . '/' .$row[1]);
           $this->iExpectAResponseCode(200);
            }
    }

    /**
     * @When I watch each repository
     */
    public function iWatchEachRepository()
    {
        $parameters = json_encode(['subscribed' => 'true']);

        foreach ($this->table as $row){
            $watch_url = '/repos/' .$row['name'] ;
            $this->client->get($watch_url, ['body' => $parameters]);
        }
    }


    /**
     * @Then My watch list will include those repositories
     * @throws Exception
     */
    public function myWatchListWillIncludeThoseRepositories()
    {
        $watch_url = '/users/' . $this->username . '/subscriptions';
        $this->response = $this->client->get($watch_url);
        $watches = $this->getBodyAsJson();

        foreach($this->table as $row){
            $fullname = $row['name'];

            // Check to see if the name from our project itself that we've
            // already put together does that exist in the watches that we have currently
            foreach ($watches as $watch){
                if($fullname == $watch['full_name']){
                    break 2; //break at 2 loops
                }
            }
            throw new Exception("Error!" .$this->username . " is not watching " . $fullname);
        }


    }


}

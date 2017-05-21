[Question](https://drive.google.com/file/d/0B41g5NdEJiyXbG5sNXM3ZGt2SGJ0cHA5RmZDNkdOTnlkWVlR/view)

### Briefly describe the conceptual approach you chose?
   * **Models**
        * **Rule** :- One rule can be pointed to by multiple Rule Engines
            * A rule consists of **variable/signal_name, operator, and expected value**.
            * List of possible operators are pre-determined and mapped to actual arithmetic operators.
            * Rule can be evaluated by evaluating given expression operator expected expression

        * **Rule Engine** :- One Rule Engine can have many rules. Several rule enggines can point to the same Rule. Thus a Many=>Many mapping exists.
            * Support to **add Rule** and **loadAllRules** is present
            * Support to **evaluate data** for allRules is present.
            * **Versioning Support** for RuleEngine Present but needs to evolve.
            * There can be **multiple Rule Engines**.
            
    * **Services**
        * **RuleEvaluatorService** :- It takes in a ruleEngine in its constructor. Then processes Data.
            * Processing of Data can be done **synchronously** or **asynchronously**.
            * If processing is asynchronous then a queue is maintained for processing.
            * If processing is asynchronous then **observer** object is also passed in the processing function so that it is updated when processing of data is complete.
            * The **observer** has the **responsibilty** to understand what to do if the evaluation fails. In this case we are simple **printing the signal_name**.
    
### What are the trade-offs?
* Rules  can exist without being mapped to RuleEngines
* All English language operators or combinations cannot be used as part of a rule
* JSON file parsing is not a concern of the models. Ideally storage format should be a blackbox for the models
* Support for OR functions is not provided.

### Runtime performance, Complexity and Bottlenecks
* Presently all Rules being evaluated for each data. However this can be improved by storing an associative map of rules by signal_name and then data will be evaluated only for rules of that signal_type.
* Async support is provided so called may choose to handle results without waiting for processing to complere.
* Support for new operands may be complex
* Error handling not done

### if you had more time, what improvements would you make, and in what order of priority?
* **Error handling** to have sense when things are failing and logging of errors for debugging purposes as well as to re-run processing.
* Better **unit testing** along with mocking perhaps.
* Support for **OR functionality** which might require some data storage changes
* Make **queue persistent** so that service restarts do not hamper processing
* **Improved versioning** We might choose to dynamically increase version number whenever new Rule is added.
* Improvements already mentioned in other sections like removing concern of json parsing and reading and writign model data and loading model. Also the ways rules are present in a RuleEngine object so that processing is Removed.
* Ability to delete Rules from Rule Engine.
* Add support for more complex rules like **before 2 days from now**
* **Parallel Processing** of Data
* **Singleton for a Particular RuleEngine and Version no** so that same object can be passed around and data queued in it.



 ### your solution satisfies the requirements ?
 Running test.php will load the data. 
 Rules could have been previously added or can be added while running. 
 Eventually the signal names will be printed. There will be repeats if same signal_name has multiple data entries being processed but that can be changed. There was some ambiguity of that the exact output should be but the observer can change what needs to be done.
 
 ### How is code and functionality tested.
 
 Code is tested by writing small scripts which acted as unit tests. However they are not part of this repo and proper unit testing support with asserts need to be added.
 
 ### Understandability and maintability of Code
 
 Some code redundancy and separation of concerns need to be implemented. However basic model structure and versioning and using sync as well as async support is present. New Rule Engines can be easily added. New observers with different functionality can be easily created.
 
 ### Cleanliness of Design and Implementation
 
 Basic structure and design maintained. Singleton can be included. However need to spend more time to improve separation of concerns and need to investigate if there are better design patterns.

### Performance on Standard laptop

Performance can be improved and suggestions have been mentioned in the TODOS.

### Answers to Discussion Questions

Will keep adding of what questions come to my mind before the discussion.


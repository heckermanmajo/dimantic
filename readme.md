# Dimantic

__A social network based on long-form dialogue.__

We need to design a system that would even publicise the truth spoken by a peasant 
if it would be used in the 14th century, where thought in society was
lead by the church and the king.

## Project Goal
Society is a complex system - a cybernetic organism.
Social networks are their nervous system - collective intelligence (CI).
All current social networks suck.
Networks based sole on shareholder value as their optimization function
will all tend to addiction-drive ape-content factories; 
Therefore modernity feels like a collective psychosis -
A giant seizure of mankind.

We need to develop a network that fixes the attention of 
the most competent minds - and distribute the most important
information in every category to the most people.

So we cannot use shareholder value as our optimization function.

## What is a social network?
A social network is a means of communication.

The format counts - a means of communication is not just a tool, 
it also influences the culture and ideals of communication. It is 
a factor in the evolution of society itself.

The platform as a whole creates a own culture by means of its
way of communication. Therefore, all changes towards a better 
society must be based on a better social network.

While short term, high frequency, high stimulus communication
creates a culture of short term thinking, superficiality 
optimization of personal appearance instead of personal substance,
long form, low frequency, deep stimulus communication
creates a culture of long term thinking, depth
and optimization of personal substance instead of personal appearance.

So far the theory.

## Project Structure (not yet fully figured out)

The project is headed by a core team of 3 people, but 
everybody is invited to contribute.

The code is open source - but is owned by the Dimantic Foundation 
(not yet a legal entity).

We don't optimize for shareholder value, but every social network
needs a business-model. 

To be able to keep the ideal - dimantic needs multiple sources of income.
This way we can balance out the different interests.

More infos at https://dimantic.org/project.php

## Code Style

Simple and explicit is king.
As few libs, frameworks, steps, dependencies as possible.
Each addition is a cost and need to provide overwhelming value.
Removal of complexity is the highest goal - god kisses the 
coder who makes the code easier to understand and change.

Flexible code is easy-to-understand-code, flexible code is 
NOT abstract code. Most of the time you DON'T reuse code. 
Only think about re-usability if you have already typed the same lines 
3 times. 

Think - not blindly follow any rule.

No "always do X", the only always rule is: always be able to explain
why the way you did it is better for the goal of the project.

ClassName, $variable_name, function_name, CONSTANT_NAME

Comments are nice. Write them. You can always delete them later...

## More Documentation

To make it easy to understand the code go on 

  https://dimantic.org/project.php

Here are Videos about the Idea of the project, its philosophy and
MOST IMPORTANTLY: introductions to the code, why I did it this way
what files do, the overarching concepts and so on.

This will greatly improve your understanding of the code.

## Deployment

Clone.

You only want (and are allowed) to run it locally.

Just run:

    path_to_directory_of_dimantic$ cd app && php -S localhost:8000

Make sure to start the server after c-d ing into the "app" directory.

Install php 8.2 locally and enable the following extensions:
-curl 
-pdo_sqlite
-pdo_mysql
(list not yet complete)

Ensure to enable asserts in the php.ini file:

    zend.assertions = 1

(ask chatgpt and/or google how to do that)

## Contribute

The main place to get in tuch is the discord server:

    https://discord.gg/WDuzWPnj

If you want to contribute code, just fork the repo and make a pull request.
It is however recommended to first get in touch with the core team - namely hackermanmajo
and talk about needed features and how to implement them.
This will ensure your effort is not wasted, since the project is at an early 
state and changes can be quite big and fast.

ESPECIALLY IF YOU WANT TO CHANGE PARADIGMS OR THE OVERARCHING CONCEPTS
OF HOW STUFF IS DONE, TALK TO THE CORE TEAM FIRST.

If this project is a success, we will make sure that contributors 
are rewarded for their effort.

As said: visit the discord server and https://dimantic.org/project.php for more info.

## License 
Code belongs to the Dimantic-Team, namely to marvin-joshua knapp-tietz
(owner of the repo) until a legal entity is created.

You are not allowed to host it on the internet as service in any way
or use it in any commercial context.

You are however allowed to run it locally and contribute to the project.

As said - your contributions will be rewarded, but until there is a working 
legal framework for this, you need to trust us on this.

On further questions ask me on discord.

Lets code! The people of tomorrow need us helping them today!
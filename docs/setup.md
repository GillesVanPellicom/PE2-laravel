# Local Setup Process

The default method for running this project is within a Docker container. Docker simplifies the sharing of a fully pre-configured development environment across the team, effectively preventing the classic “but it works on my machine” issue. By maintaining a single, consistent environment using the main branch, any necessary configuration changes made by one team member will automatically propagate to everyone through GitHub, ensuring mostly seamless updates and consistency.

> In this guide I'm using a lot of general terms such as "terminal", since this guide is supposed to be OS-neutral. Obviously substitute these for their correct counterparts. In this case bash, powershell, zsh or whichever flavor terminal comes with your OS.

With the preamble out of the way, here's a step-by-step guide on how to set up a local development environment for our project using Docker.

## 1) Install Docker

Go to [this link](https://www.docker.com/products/docker-desktop/) and download the correct installer for your device. Follow the provided instructions until you find yourself in the docker desktop dashboard. Make sure that if the installer asks you something about "PATH" or "shell" to check that option. This will make it so you can run CLI commands in the terminal. Restart your device if the installer hasn't asked you to do so already.

To test if you have successfully installed Docker, open a terminal and run the following command:

```bash
docker
```

If you see something along the lines of "this is how you use the command" and not "command not found", you've installed docker successfully.

## 2) Clone the repository

For this step, I'm assuming you already have [git](https://git-scm.com/downloads) installed. Make sure you are able to run the `git` command in your terminal. For windows users, if you are unable to run `git` in powershell or cmd (after a reboot), git for windows comes with it's own terminal called "git bash". You can search for it in the start menu. Alternatively, you can also use git via [WSL](https://learn.microsoft.com/en-us/windows/wsl/install).

Next, make sure you are [logged in with your Github accout on git](https://docs.github.com/en/get-started/getting-started-with-git/set-up-git). If you ommit this step you might not be able to download the repository.

Navigate to the directory where you want to store the project and run the following command:

```bash
git clone FIXME
```

git will now download the project and initialize local version control.

## 3) Initialize the docker container

Navigate to the project root directory and run the following command:

```bash
./vendor/bin/sail up -d
```

This will generate the docker container and start the webserver and database in the background. This process might take a while the first time. When the command has finished you should be able to see the container in the docker desktop dashboard.

In the docker dashboard, you can control the container. You can stop and start the containers from here. Here you can also send container specific commands, config inspection, etc.

Although there is a "delete" button associated with the container in the dashboard, I would advise against using it. If you ever want to delete the container, look [here](#in-case-of-configuration-changes).

Finally, navigate to `localhost` in your browser to verify that the app is running.

## 4) Initialize project

To finish the setup process, you'll have to run a few commands fetch/build the last dependancies.

Fetch node modules

```bash
./vendor/bin/sail npm install
```

Generate the database.

```bash

./artisan migrate:fresh
```

Seed the database with test/production data.

```bash

./artisan db:seed
```

## Considerations

Like with XAMPP or any other managed webserver, docker has to be active with the development containers enabled in order to develop. These will shut down during every OS reboot and don't start automatically.

## Changes in command syntax

You're probably used to running some variation of the following command:

```bash
php artisan <xyz>
```

This might not work in this setup.

If you experience problems with this command, you should run all artisan commands like so:

```bash
./artisan <xyz>
```

This basically leaves out the php call and just gives the command to the artisan program in the root folder of the project.



## Rebuild container

If the server configuration changes, you will have to rebuild the container. This can be done by running the following series of commands:

Take the containers down:

```bash
./vendor/bin/sail down -v
```

Rebuild the containers:

```bash
./vendor/bin/sail up -d
```

This only rebuilds/updates the development containers, not the project itself. No production code will be affected and you won't have to do any of the setup commands again.




# Github Guide

This guide will start by covering the basics of github required for this project. If you are already familiar with git, you can skip this section and move on to the procedure section.

## Understanding Git: The Basics

### What is git?

Git is a version control system (VCS), which means it helps manage changes to files over time. It allows multiple people to work on the same project simultaneously without overwriting each other’s work. Also, in case of an error, you can revert to a previous version of your project. Never again do you need to figure out what you changed to break your code in order to reverse it, there's a command for that.

### Key Concepts in Git

1. **Repository**: A repo is a collection of files and the history of changes made to those files. A repo can be either local (on your computer) or remote (on GitHub, GitLab, etc.).
2. **Commit**: A commit is like a snapshot of your project at a particular point in time. It captures the changes made to the files in your repository.
3. **Staging** Area: Before committing, files are in the staging area. Here you choose which changes you want to commit.
4. **Remote Repository**: A version of your repository hosted on a platform like GitHub. Changes can be pulled from or pushed to this remote repository.
5. **Delta**: The difference between two commits. Commit_2 - Commit_1 = delta. This is what git uses to track changes.
6. **Branch**: A branch is a parallel version of your repository. You split off another branch, so your new branch will have code stuck in time at that point. You can create a branch to work on a new feature without affecting the development branches. Once you are done, you can merge the branch back into the development branch.


## 1) Git setup

> If you followed the [Local Setup Guide](/docs/setup.md), you can skip to the procedure.


Git is the version control system we will be using for this project. If you don't have git installed on your machine, you can download it [here](https://git-scm.com/downloads).

The distinction between git and Github is important. Git is the actual VCS, while github is a platform that hosts git repositories online, allowing for collaboration. You can use git locally without Github, but you can't use Github without git.

In order to be able to interact with our Github repository, you'll need to be logged in with your Github account on git. To get this started, input the following commands:

```bash
git config --global user.name "Your name here"
git config --global user.email "youremailhere@example.com"
```
Replace the name and email with your own.

Your name will be displayed on your commits, and your email is used to associate your commits with your Github account.
Make sure to use your real surname here, as this will be displayed on your commits.
Also make sure to use the email address associated with your Github account, this will not work otherwise.

Next time you use a git command which requires authentication, Github will prompt you to log in. Follow these instructions.

For security, Github has disabled password-based authentication. You'll have to generate a [Personal Access Token (PAT)](https://github.com/settings/tokens) to authenticate. Don't do this now, do this when asked.

## 2) Cloning the repository

Now we will make a local copy of the repository on your machine. This is called cloning. 

Navigate to the directory where you want to store the project and run the following command:

```bash
git clone https://github.com/GillesVanPellicom/PE2-laravel/
```

If you are reading this step and didn't skip to step the procedure, you probably didn't follow the [Local Setup Guide](/docs/setup.md), and have chosen to use your own solution to run the project. Before continuing, make sure you get your instance of the project running, since otherwise there is a chance you'll lose work by having to do trial-and-error.

Git should recognise this as a local repository and initialize local version control. You can test this like so:

```bash
git status
```

If this command doesn't error and gives you the current repository status, you've successfully cloned the repository.

The `git status` command can be used to see the current status of the branch you are working on, and is the most basic tool to see what's going on in your repository. You might not understand everything you have just read, but it's worth remembering the command.

## Procedure

Coming soon™

Philosophy on branching, merging, etc. has to be discussed in group first.
Contribution Guide for Laravel Repository Pattern Package

Thank you for considering contributing to the Laravel Repository Pattern Package! We welcome contributions that help improve the package, fix issues, or add new features. Here is a guide to help you get started.

How to Contribute

1. Fork the Repository

Start by forking the repository on GitHub. This will create a copy of the repository under your GitHub account, which you can work on freely.

2. Clone Your Fork

Clone your fork to your local machine to start making changes:

git clone https://github.com/your-username/laravel-repository-pattern.git

Navigate into the project directory:

cd laravel-repository-pattern

3. Create a Branch

Create a new branch to isolate your changes:

git checkout -b feature/your-feature-name

4. Make Your Changes

Make the necessary changes or improvements. Please follow the existing coding standards and style conventions used in the package. Some examples of potential contributions include:

Bug fixes.

Adding new features (e.g., new repository methods).

Improving documentation.

Enhancing test coverage.

5. Test Your Changes

Make sure that your changes do not break existing functionality by running the tests. If applicable, write new tests for any new functionality.

To run the tests, use:

./vendor/bin/phpunit

6. Commit Your Changes

Once you're satisfied with your changes, commit them with a meaningful commit message:

git add .
git commit -m "Add a brief description of your changes"

7. Push to Your Fork

Push the changes to your forked repository:

git push origin feature/your-feature-name

8. Create a Pull Request

Go to the original repository on GitHub, and you should see an option to create a Pull Request (PR). Submit the PR and provide a descriptive title and summary of the changes you made.

Code of Conduct

Please follow the Contributor Covenant Code of Conduct to maintain a welcoming and friendly community.

Reporting Issues

If you encounter any bugs or issues, please create an issue on GitHub with detailed information about the problem, steps to reproduce, and any relevant context. We appreciate your help in identifying areas for improvement.

Pull Request Guidelines

Keep PRs focused: A pull request should focus on a single change or feature.

Write meaningful descriptions: Provide enough detail so that others can understand what the PR is doing.

Add tests when necessary: If your changes include new features, make sure to include relevant tests.

Publishing Configuration

After installing the package, you may need to publish the configuration file:

php artisan vendor:publish --tag=repository-config

This allows customization of the repository settings, such as enabling caching or defining default methods.

Questions?

If you have any questions or need help, feel free to create a GitHub issue or reach out to us directly. We appreciate all contributions and look forward to building an even better package together!


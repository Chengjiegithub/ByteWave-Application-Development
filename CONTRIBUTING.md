# Contributing to GPSphere

Thank you for your interest in contributing to GPSphere! This document provides guidelines and instructions for contributing.

## 🚀 Getting Started

1. **Fork the repository**
2. **Clone your fork**
   ```bash
   git clone https://github.com/your-username/GSphere.git
   cd GSphere
   ```
3. **Create a branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

## 📝 Development Setup

1. **Install dependencies**
   ```bash
   cd nodejs
   npm install
   ```

2. **Set up environment variables**
   - Copy `.env.example` to `.env` (if available)
   - Configure database and email settings

3. **Initialize database**
   ```bash
   node scripts/initDb.js
   ```

4. **Start development server**
   ```bash
   npm run dev
   ```

## 🔧 Code Style

- Follow existing code style and conventions
- Use meaningful variable and function names
- Add comments for complex logic
- Keep functions focused and modular

## 📋 Commit Guidelines

- Use clear, descriptive commit messages
- Follow conventional commit format:
  - `feat:` for new features
  - `fix:` for bug fixes
  - `docs:` for documentation changes
  - `style:` for formatting changes
  - `refactor:` for code refactoring
  - `test:` for test additions/changes

Example:
```
feat: Add event filtering by date range
fix: Resolve TAC email sending issue
docs: Update API documentation
```

## 🧪 Testing

- Test your changes thoroughly before submitting
- Ensure existing functionality still works
- Test edge cases and error handling

## 📤 Submitting Changes

1. **Ensure your code is up to date**
   ```bash
   git pull origin main
   ```

2. **Commit your changes**
   ```bash
   git add .
   git commit -m "feat: Add your feature description"
   ```

3. **Push to your fork**
   ```bash
   git push origin feature/your-feature-name
   ```

4. **Create a Pull Request**
   - Go to the original repository
   - Click "New Pull Request"
   - Select your branch
   - Fill out the PR template
   - Submit for review

## ✅ Pull Request Checklist

- [ ] Code follows project style guidelines
- [ ] Self-review completed
- [ ] Comments added for complex code
- [ ] Documentation updated (if needed)
- [ ] No console.log or debug code left
- [ ] Changes tested locally
- [ ] No merge conflicts

## 🐛 Reporting Bugs

When reporting bugs, please include:
- Clear description of the issue
- Steps to reproduce
- Expected vs actual behavior
- Environment details (OS, Node version, etc.)
- Screenshots (if applicable)

## 💡 Feature Requests

For feature requests:
- Describe the feature clearly
- Explain the use case
- Suggest implementation approach (if you have ideas)

## 📚 Documentation

- Update README.md if adding new features
- Add/update API documentation
- Include code comments for complex logic

## 🤝 Code Review Process

- All PRs require review before merging
- Address review comments promptly
- Be open to feedback and suggestions

## 📞 Questions?

If you have questions, feel free to:
- Open an issue for discussion
- Contact the maintainers
- Check existing documentation

Thank you for contributing to GPSphere! 🎉


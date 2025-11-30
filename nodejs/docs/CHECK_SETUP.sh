#!/bin/bash
# 📋 BYTEWAVE NODEJS MIGRATION CHECKLIST

echo "🚀 ByteWave PHP → Node.js Migration Checklist"
echo "=============================================="
echo ""

# Check Node.js
echo "✓ Checking Node.js installation..."
if command -v node &> /dev/null; then
    node_version=$(node --version)
    echo "  ✅ Node.js installed: $node_version"
else
    echo "  ❌ Node.js NOT installed - Download from https://nodejs.org/"
    exit 1
fi

# Check npm
echo "✓ Checking npm installation..."
if command -v npm &> /dev/null; then
    npm_version=$(npm --version)
    echo "  ✅ npm installed: $npm_version"
else
    echo "  ❌ npm NOT installed"
    exit 1
fi

# Check MySQL
echo "✓ Checking MySQL connection..."
if pgrep -x "mysql" > /dev/null; then
    echo "  ✅ MySQL is running"
else
    echo "  ⚠️  MySQL may not be running - Start MySQL and try again"
fi

# Check directory structure
echo ""
echo "✓ Checking directory structure..."
files_to_check=(
    "server.js"
    "initDb.js"
    "package.json"
    ".env"
    "src/config/database.js"
    "src/controllers/authController.js"
    "src/controllers/userController.js"
    "src/controllers/eventController.js"
    "src/controllers/chatbotController.js"
    "src/routes/authRoutes.js"
    "src/routes/userRoutes.js"
    "src/routes/eventRoutes.js"
    "src/routes/chatbotRoutes.js"
    "src/middleware/auth.js"
    "src/utils/email.js"
    "public/index.html"
)

all_files_present=true
for file in "${files_to_check[@]}"; do
    if [ -f "$file" ]; then
        echo "  ✅ $file"
    else
        echo "  ❌ $file - MISSING"
        all_files_present=false
    fi
done

# Check node_modules
echo ""
echo "✓ Checking dependencies..."
if [ -d "node_modules" ]; then
    module_count=$(ls -1 node_modules | wc -l)
    echo "  ✅ Dependencies installed ($module_count packages)"
else
    echo "  ❌ Dependencies NOT installed - Run: npm install"
fi

# Check .env file
echo ""
echo "✓ Checking .env configuration..."
if [ -f ".env" ]; then
    if grep -q "DB_HOST" .env; then
        echo "  ✅ .env file exists with database config"
        if grep -q "JWT_SECRET" .env; then
            echo "  ✅ JWT_SECRET configured"
        else
            echo "  ⚠️  JWT_SECRET missing - Add to .env"
        fi
        if grep -q "EMAIL_USER" .env; then
            echo "  ✅ Email configuration found"
        else
            echo "  ⚠️  Email configuration incomplete"
        fi
    else
        echo "  ❌ .env file incomplete"
    fi
else
    echo "  ❌ .env file NOT found - Create it first"
fi

# Final status
echo ""
echo "=============================================="
if [ "$all_files_present" = true ] && [ -d "node_modules" ] && [ -f ".env" ]; then
    echo "✅ All systems ready! Start with:"
    echo "   npm run dev"
    echo ""
    echo "Then test at: http://localhost:3000"
else
    echo "⚠️  Please fix issues above before starting"
    echo ""
    echo "Setup steps:"
    echo "1. npm install"
    echo "2. Create .env file"
    echo "3. node initDb.js"
    echo "4. npm run dev"
fi
echo "=============================================="

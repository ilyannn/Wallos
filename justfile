# Wallos justfile - Main development commands
# Usage: just <command>
# Image configuration (DRY)

image := "wallos"
tag := "latest"
image_tag := image + ":" + tag

# Default command to show available commands
_list:
    @just --list

# Build the Docker image
build:
    @echo "Building Wallos Docker image..."
    docker build -t {{ image_tag }} .

# Start the Docker services
start:
    @echo "Starting Wallos Docker services..."
    docker compose up -d

# Start development mode with bind mounts (no rebuild needed)
dev:
    @echo "Starting Wallos in development mode with bind mounts..."
    @echo "Ensuring clean state..."
    docker compose -f docker-compose.yaml -f docker-compose.dev.yaml down 2>/dev/null || true
    @echo "Checking file permissions..."
    @chmod -R 755 includes endpoints scripts styles api images libs migrations 2>/dev/null || true
    @chmod 644 *.php *.js *.json 2>/dev/null || true
    @echo "Starting development containers..."
    docker compose -f docker-compose.yaml -f docker-compose.dev.yaml up -d
    @echo "Development mode started! Use 'just logs' to view output."

# Stop the Docker services
stop:
    @echo "Stopping Wallos Docker services..."
    docker compose down

# Stop development mode
dev-stop:
    @echo "Stopping Wallos development mode..."
    docker compose -f docker-compose.yaml -f docker-compose.dev.yaml down

# Restart the Docker services
restart:
    @echo "Restarting Wallos Docker services..."
    docker compose restart

# View running containers and logs
logs:
    @echo "Showing Wallos container logs..."
    docker compose logs -f

# View container status
status:
    @echo "Container status:"
    docker compose ps

# Open Wallos in the default browser
open:
    @echo "Opening Wallos in browser..."
    open http://localhost:8282

# Run Superlinter on the codebase
superlint:
    @echo "Running Superlinter on Wallos codebase..."
    docker run --rm \
        -e RUN_LOCAL=true \
        -e VALIDATE_ALL_CODEBASE=true \
        -e VALIDATE_JSCPD=false \
        -e VALIDATE_PHP_PHPCS=false \
        -e VALIDATE_PHP_PHPSTAN=false \
        -e VALIDATE_PHP_PSALM=false \
        -v $(pwd):/tmp/lint \
        -w /tmp/lint \
        github/super-linter:latest

# Run Superlinter only on PR changes (much faster)
superlint-pr:
    @echo "Running Superlinter on PR changes only..."
    @echo "Detecting changed files since main branch..."
    @git diff --name-only origin/main...HEAD
    @echo "Running Super-Linter with file filtering..."
    docker run --rm \
        -e RUN_LOCAL=true \
        -e VALIDATE_ALL_CODEBASE=false \
        -e DEFAULT_BRANCH=main \
        -e VALIDATE_JSCPD=false \
        -e VALIDATE_PHP_PHPCS=false \
        -e VALIDATE_PHP_PHPSTAN=false \
        -e VALIDATE_PHP_PSALM=false \
        -e VALIDATE_CSS=false \
        -e VALIDATE_JAVASCRIPT_STANDARD=false \
        -e VALIDATE_NATURAL_LANGUAGE=false \
        -v $(pwd):/tmp/lint \
        -w /tmp/lint \
        github/super-linter:latest

# Clean up Docker resources
clean:
    @echo "Removing local image {{ image_tag }}..."
    docker image rm -f {{ image_tag }} || true

# Rebuild: build, stop, and start
rebuild: build stop start
    @echo "Rebuild completed!"

# Full reset: stop, clean, rebuild, and start
reset: stop clean build start
    @echo "Full reset completed!"

# Troubleshoot development mode issues
dev-debug:
    @echo "=== Development Mode Troubleshooting ==="
    @echo "1. Checking file existence:"
    @ls -la *.php | head -5
    @echo "2. Checking directory permissions:"
    @ls -ld includes endpoints scripts styles | head -4
    @echo "3. Checking Docker containers:"
    docker compose -f docker-compose.yaml -f docker-compose.dev.yaml ps
    @echo "4. Checking Docker volumes:"
    docker compose -f docker-compose.yaml -f docker-compose.dev.yaml config --volumes
    @echo "5. Recent container logs:"
    docker compose -f docker-compose.yaml -f docker-compose.dev.yaml logs --tail=10

# Clean development environment (fixes mount issues)
dev-clean:
    @echo "Cleaning development environment..."
    docker compose -f docker-compose.yaml -f docker-compose.dev.yaml down -v
    docker system prune -f
    @echo "Development environment cleaned. Run 'just dev' to restart."

fmt:
    just --format --unstable
    bun x --bun prettier --write CLAUDE.md docker-compose.yaml docker-compose.dev.yaml

# Makefile for Windows using built-in commands
# Simple zip compression without PowerShell restrictions

PROJECT_NAME = shares_transition_notification
ZIP_DIR = zip

.PHONY: help
help:
	@echo "Windows Makefile for ZIP compression"
	@echo "Available targets:"
	@echo "  zip-python    - Create zip with Python files"
	@echo "  zip-lambda    - Create Lambda deployment package"
	@echo "  zip-all       - Create zip with all project files"
	@echo "  clean         - Remove generated zip files"
	@echo "  list          - List created zip files"
	@echo "  test          - Test the makefile"

# Create directory using mkdir
$(ZIP_DIR):
	@if not exist $(ZIP_DIR) mkdir $(ZIP_DIR)

# Test target
.PHONY: test
test:
	@echo ✅ Makefile is working!
	@echo Project: $(PROJECT_NAME)
	@echo Zip directory: $(ZIP_DIR)
	@echo Current directory: %CD%

# Simple zip using tar (available in Windows 10+)
.PHONY: zip-python
zip-python: $(ZIP_DIR)
	@echo Creating Python files zip...
	@tar -czf $(ZIP_DIR)\$(PROJECT_NAME)-python.zip shares_transition_notification.py 2>nul || echo No Python files found
	@echo ✅ Created: $(ZIP_DIR)\$(PROJECT_NAME)-python.zip

# Lambda deployment package (optimized for AWS Lambda)
.PHONY: zip-lambda
zip-lambda: $(ZIP_DIR)
	@echo Creating Lambda deployment package...
	@tar -czf $(ZIP_DIR)\$(PROJECT_NAME)-lambda.zip *.py requirements.txt 2>nul || echo Some files may not exist
	@echo ✅ Created: $(ZIP_DIR)\$(PROJECT_NAME)-lambda.zip
	@echo 📦 Ready for AWS Lambda deployment!

# Zip all relevant files
.PHONY: zip-all
zip-all: $(ZIP_DIR)
	@echo Creating complete project zip...
	@tar -czf $(ZIP_DIR)\$(PROJECT_NAME)-complete.zip *.py *.txt *.json *.yml *.yaml 2>nul || echo Some files may not exist
	@echo ✅ Created: $(ZIP_DIR)\$(PROJECT_NAME)-complete.zip

# Clean up
.PHONY: clean
clean:
	@echo Cleaning generated zip files...
	@if exist $(ZIP_DIR) rmdir /s /q $(ZIP_DIR)
	@echo ✅ Cleaned up

# List zip files
.PHONY: list
list:
	@echo Zip files in $(ZIP_DIR):
	@if exist $(ZIP_DIR) dir $(ZIP_DIR)\*.zip /b 2>nul || echo No zip files found

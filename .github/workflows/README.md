# GitHub Actions Workflows

This directory contains GitHub Actions workflows for CI/CD automation.

## Workflows

### `ci-cd.yml` - Build and Push Docker Images

Automatically builds and pushes Docker images for:
- **CMS Image** (PHP-FPM): `build/Dockerfile`
- **Nginx Image**: `build/Dockerfile.nginx`

#### Triggers

1. **Git Tags**: Automatically triggers on tags starting with `v` (e.g., `v1.0.0`)
2. **Manual**: Can be triggered manually from Actions tab

#### How to Use

**Create a Release**:
```bash
# Tag your release
git tag v1.2.3
git push origin v1.2.3

# Workflow will automatically:
# - Build CMS image with tag: 1.2.3
# - Build Nginx image with tag: 1.2.3
# - Push both images
# - Tag both as 'latest'
```

**Manual Build**:
1. Go to Actions tab in GitHub
2. Select "Build and Push Docker Images"
3. Click "Run workflow"
4. Choose build type (tag or commit)
5. Click "Run workflow"

#### Configuration

Update these variables in `ci-cd.yml`:

```yaml
env:
  REGISTRY: ghcr.io  # Change to your registry
  IMAGE_NAME_CMS: your-org/mfg-dashboard
  IMAGE_NAME_NGINX: your-org/mfg-dashboard-nginx
```

#### Registry Setup

**GitHub Container Registry** (default):
- No setup needed
- Uses `GITHUB_TOKEN` automatically
- Images: `ghcr.io/your-org/mfg-dashboard:tag`

**Docker Hub**:
1. Add secrets: `DOCKER_USERNAME`, `DOCKER_PASSWORD`
2. Update `REGISTRY: docker.io`
3. Update login action in workflow

**Other Registries**:
- Add appropriate authentication secrets
- Update registry URL
- Configure login action

## Workflow Status

View workflow runs:
- GitHub → Actions tab
- See build history, logs, and status

## Troubleshooting

**Workflow not triggering?**
- Check if tag starts with `v` (e.g., `v1.0.0`)
- Verify workflow file is in `.github/workflows/`

**Build failing?**
- Check Actions tab for error logs
- Verify Dockerfile paths are correct
- Check registry authentication

**Images not pushing?**
- Verify registry credentials/secrets
- Check registry permissions
- Verify image names are correct

## See Also

- Full migration guide: `GITHUB_ACTIONS_MIGRATION.md`
- Kubernetes deployment: `k8s/README.md`

# Kubernetes Deployment for MFG Dashboard

This directory contains Kubernetes manifests for deploying the MFG Dashboard application.

## Files Overview

- `namespace.yaml` - Creates the namespace for the application
- `configmap.yaml` - Non-sensitive configuration (timezone, timeout, etc.)
- `secret.yaml` - Sensitive configuration (database credentials, salt)
- `deployment.yaml` - Main application deployment with PHP-FPM and Nginx
- `service.yaml` - Kubernetes service to expose the application
- `ingress.yaml` - Ingress resource for external access (optional)
- `hpa.yaml` - Horizontal Pod Autoscaler for automatic scaling (optional)

## Prerequisites

1. Kubernetes cluster (v1.19+)
2. kubectl configured to access your cluster
3. Docker image built and pushed to a container registry
4. MySQL database accessible from the cluster
5. (Optional) Ingress controller installed (e.g., NGINX Ingress Controller)
6. (Optional) cert-manager for TLS certificates

## Quick Start

### 1. Update Secrets

**IMPORTANT**: Before deploying, update the secret values in `secret.yaml`:

```bash
# Edit secret.yaml and replace placeholder values:
# - DB_HOST: Your MySQL service hostname
# - DB_NAME: Your database name
# - DB_USER: Your database username
# - DB_PASSWORD: Your secure database password
# - APP_SALT: A random secure string for password encryption
```

Or create the secret manually:

```bash
kubectl create secret generic mfg-dashboard-secret \
  --from-literal=DB_HOST=mysql-service.default.svc.cluster.local \
  --from-literal=DB_NAME=mode_dashboard \
  --from-literal=DB_USER=dashboard_user \
  --from-literal=DB_PASSWORD=your-secure-password \
  --from-literal=DB_CHARSET=utf8mb4 \
  --from-literal=DB_PORT=3306 \
  --from-literal=APP_SALT=your-random-salt-string \
  --namespace=mfg-dashboard
```

### 2. Update Deployment Image

Edit `deployment.yaml` and replace `mfg-dashboard:latest` with your actual image:

```yaml
containers:
- name: php-fpm
  image: your-registry/mfg-dashboard:v1.0.0  # Update this
```

### 3. Update Ingress (if using)

Edit `ingress.yaml` and update:
- `host`: Your domain name
- `ingressClassName`: Your ingress class name
- Uncomment TLS section if using SSL

### 4. Deploy

Apply all manifests:

```bash
# Create namespace
kubectl apply -f namespace.yaml

# Create configmaps
kubectl apply -f configmap.yaml

# Create secret (after updating values)
kubectl apply -f secret.yaml

# Deploy application
kubectl apply -f deployment.yaml

# Create service
kubectl apply -f service.yaml

# (Optional) Create ingress
kubectl apply -f ingress.yaml

# (Optional) Create HPA
kubectl apply -f hpa.yaml
```

Or apply all at once:

```bash
kubectl apply -f .
```

### 5. Verify Deployment

```bash
# Check pods
kubectl get pods -n mfg-dashboard

# Check services
kubectl get svc -n mfg-dashboard

# Check ingress
kubectl get ingress -n mfg-dashboard

# View logs
kubectl logs -f deployment/mfg-dashboard -n mfg-dashboard -c php-fpm
kubectl logs -f deployment/mfg-dashboard -n mfg-dashboard -c nginx
```

## Configuration

### Environment Variables

The application reads configuration from:
- **ConfigMap** (`mfg-dashboard-config`): Non-sensitive settings
- **Secret** (`mfg-dashboard-secret`): Sensitive credentials

See `KUBERNETES_ENV.md` in the root directory for complete environment variable documentation.

### Updating Configuration

```bash
# Update ConfigMap
kubectl edit configmap mfg-dashboard-config -n mfg-dashboard

# Update Secret
kubectl edit secret mfg-dashboard-secret -n mfg-dashboard

# Restart pods to apply changes
kubectl rollout restart deployment/mfg-dashboard -n mfg-dashboard
```

## Scaling

### Manual Scaling

```bash
kubectl scale deployment mfg-dashboard --replicas=3 -n mfg-dashboard
```

### Automatic Scaling (HPA)

If HPA is deployed, it will automatically scale based on CPU and memory usage:
- Min replicas: 2
- Max replicas: 10
- CPU target: 70%
- Memory target: 80%

## Troubleshooting

### Pods not starting

```bash
# Check pod status
kubectl describe pod <pod-name> -n mfg-dashboard

# Check events
kubectl get events -n mfg-dashboard --sort-by='.lastTimestamp'
```

### Database connection issues

```bash
# Test database connectivity from a pod
kubectl exec -it <pod-name> -n mfg-dashboard -c php-fpm -- sh
# Inside pod:
php -r "new PDO('mysql:host=DB_HOST;dbname=DB_NAME', 'DB_USER', 'DB_PASSWORD');"
```

### View application logs

```bash
# PHP-FPM logs
kubectl logs -f deployment/mfg-dashboard -n mfg-dashboard -c php-fpm

# Nginx logs
kubectl logs -f deployment/mfg-dashboard -n mfg-dashboard -c nginx
```

### Access application shell

```bash
kubectl exec -it deployment/mfg-dashboard -n mfg-dashboard -c php-fpm -- sh
```

## Production Considerations

1. **Image Security**: Use specific image tags, not `latest`
2. **Resource Limits**: Adjust based on your workload
3. **Persistent Storage**: Consider using PersistentVolumes for:
   - `/var/www/html/protected/runtime` (logs, cache)
   - `/var/www/html/assets` (uploaded files)
4. **Backup**: Implement database backup strategy
5. **Monitoring**: Add Prometheus metrics and Grafana dashboards
6. **Logging**: Consider centralized logging (ELK, Loki, etc.)
7. **SSL/TLS**: Enable TLS in ingress for production
8. **Network Policies**: Implement network policies for security

## Cleanup

To remove all resources:

```bash
kubectl delete -f .
# Or delete namespace (removes everything)
kubectl delete namespace mfg-dashboard
```

## Support

For issues or questions, refer to:
- `KUBERNETES_ENV.md` - Environment variable documentation
- `memory-bank/` - Project documentation

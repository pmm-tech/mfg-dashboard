# MFG Dashboard - Kubernetes Deployment Guide

## Overview

This guide provides step-by-step instructions for deploying the MFG Dashboard application on Kubernetes.

## Architecture

The deployment consists of:
- **PHP-FPM Container**: Runs the PHP application
- **Nginx Container**: Web server (sidecar pattern)
- **MySQL Database**: External service (not included in these manifests)
- **ConfigMap**: Non-sensitive configuration
- **Secret**: Sensitive credentials
- **Service**: Internal load balancer
- **Ingress**: External access (optional)
- **HPA**: Auto-scaling (optional)

## Prerequisites Checklist

- [ ] Kubernetes cluster (v1.19+) with kubectl configured
- [ ] Container registry access (Docker Hub, GCR, ECR, etc.)
- [ ] MySQL database accessible from cluster
- [ ] (Optional) Ingress controller installed
- [ ] (Optional) cert-manager for TLS certificates
- [ ] (Optional) Storage class for persistent volumes

## Step-by-Step Deployment

### Step 1: Build and Push Docker Image

```bash
# Build the image
docker build -t your-registry/mfg-dashboard:v1.0.0 -f Dockerfile .

# Push to registry
docker push your-registry/mfg-dashboard:v1.0.0
```

### Step 2: Prepare Secrets

**Option A: Using kubectl (Recommended for first-time setup)**

```bash
kubectl create namespace mfg-dashboard

kubectl create secret generic mfg-dashboard-secret \
  --from-literal=DB_HOST=mysql-service.default.svc.cluster.local \
  --from-literal=DB_NAME=mode_dashboard \
  --from-literal=DB_USER=dashboard_user \
  --from-literal=DB_PASSWORD='your-secure-password' \
  --from-literal=DB_CHARSET=utf8mb4 \
  --from-literal=DB_PORT=3306 \
  --from-literal=APP_SALT='your-random-salt-string' \
  --namespace=mfg-dashboard
```

**Option B: Using secret.yaml (Update values first)**

1. Edit `secret.yaml` and replace placeholder values
2. Apply: `kubectl apply -f secret.yaml`

**Generate a secure salt:**
```bash
# Generate random salt
openssl rand -base64 32
```

### Step 3: Update Configuration Files

1. **Update deployment.yaml**: Replace `mfg-dashboard:latest` with your image
   ```yaml
   image: your-registry/mfg-dashboard:v1.0.0
   ```

2. **Update ingress.yaml** (if using ingress):
   - Change `host` to your domain
   - Update `ingressClassName` if needed
   - Uncomment TLS section for SSL

3. **Update configmap.yaml** (optional):
   - Adjust timezone, timeout, pagination size as needed

### Step 4: Deploy Application

```bash
# Navigate to k8s directory
cd k8s

# Apply all resources
kubectl apply -f namespace.yaml
kubectl apply -f configmap.yaml
kubectl apply -f secret.yaml
kubectl apply -f deployment.yaml
kubectl apply -f service.yaml

# Optional: Ingress and HPA
kubectl apply -f ingress.yaml
kubectl apply -f hpa.yaml
```

Or apply all at once:
```bash
kubectl apply -f .
```

### Step 5: Verify Deployment

```bash
# Check namespace
kubectl get ns mfg-dashboard

# Check pods (wait for Running status)
kubectl get pods -n mfg-dashboard -w

# Check services
kubectl get svc -n mfg-dashboard

# Check ingress (if deployed)
kubectl get ingress -n mfg-dashboard

# View pod logs
kubectl logs -f deployment/mfg-dashboard -n mfg-dashboard -c php-fpm
kubectl logs -f deployment/mfg-dashboard -n mfg-dashboard -c nginx

# Describe pod for detailed status
kubectl describe pod -l app=mfg-dashboard -n mfg-dashboard
```

### Step 6: Test Application

**Option A: Port Forward (for testing)**

```bash
kubectl port-forward svc/mfg-dashboard 8080:80 -n mfg-dashboard
# Access at http://localhost:8080
```

**Option B: Using Ingress**

If ingress is configured, access via the domain name specified in `ingress.yaml`.

**Option C: Cluster IP (from within cluster)**

```bash
# Get service IP
kubectl get svc mfg-dashboard -n mfg-dashboard

# Test from another pod in cluster
kubectl run -it --rm debug --image=curlimages/curl --restart=Never -- curl http://mfg-dashboard.mfg-dashboard.svc.cluster.local
```

## Configuration Reference

### Environment Variables

| Variable | Source | Description |
|----------|--------|-------------|
| `DB_HOST` | Secret | MySQL hostname |
| `DB_NAME` | Secret | Database name |
| `DB_USER` | Secret | Database username |
| `DB_PASSWORD` | Secret | Database password |
| `DB_CHARSET` | Secret | Character set |
| `DB_PORT` | Secret | Database port |
| `APP_SALT` | Secret | Password encryption salt |
| `APP_TIMEZONE` | ConfigMap | Application timezone |
| `APP_TIMEOUT` | ConfigMap | Session timeout (seconds) |
| `APP_ADMIN_EMAIL` | ConfigMap | Admin email |
| `APP_COMPANY_NAME` | ConfigMap | Company name |
| `APP_PAGINATION_SIZE` | ConfigMap | Pagination size |

## Updating Configuration

### Update ConfigMap

```bash
# Edit directly
kubectl edit configmap mfg-dashboard-config -n mfg-dashboard

# Or update file and apply
kubectl apply -f configmap.yaml
```

### Update Secret

```bash
# Edit directly (base64 encoded)
kubectl edit secret mfg-dashboard-secret -n mfg-dashboard

# Or recreate
kubectl delete secret mfg-dashboard-secret -n mfg-dashboard
kubectl create secret generic mfg-dashboard-secret \
  --from-literal=DB_PASSWORD='new-password' \
  --namespace=mfg-dashboard
```

### Restart Pods

After updating config, restart pods to apply changes:

```bash
kubectl rollout restart deployment/mfg-dashboard -n mfg-dashboard
```

## Scaling

### Manual Scaling

```bash
kubectl scale deployment mfg-dashboard --replicas=5 -n mfg-dashboard
```

### Auto Scaling (HPA)

If HPA is deployed, it automatically scales based on:
- CPU usage (target: 70%)
- Memory usage (target: 80%)
- Min replicas: 2
- Max replicas: 10

View HPA status:
```bash
kubectl get hpa -n mfg-dashboard
kubectl describe hpa mfg-dashboard-hpa -n mfg-dashboard
```

## Monitoring & Debugging

### View Logs

```bash
# All containers in deployment
kubectl logs -f deployment/mfg-dashboard -n mfg-dashboard --all-containers=true

# Specific container
kubectl logs -f deployment/mfg-dashboard -n mfg-dashboard -c php-fpm
kubectl logs -f deployment/mfg-dashboard -n mfg-dashboard -c nginx

# Specific pod
kubectl logs -f <pod-name> -n mfg-dashboard -c php-fpm
```

### Pod Status

```bash
# Get pod status
kubectl get pods -n mfg-dashboard

# Describe pod (detailed info)
kubectl describe pod <pod-name> -n mfg-dashboard

# Check events
kubectl get events -n mfg-dashboard --sort-by='.lastTimestamp'
```

### Execute Commands in Pod

```bash
# Access PHP-FPM container
kubectl exec -it deployment/mfg-dashboard -n mfg-dashboard -c php-fpm -- sh

# Access Nginx container
kubectl exec -it deployment/mfg-dashboard -n mfg-dashboard -c nginx -- sh

# Run PHP command
kubectl exec deployment/mfg-dashboard -n mfg-dashboard -c php-fpm -- php -v
```

### Database Connection Test

```bash
# Test from pod
kubectl exec -it deployment/mfg-dashboard -n mfg-dashboard -c php-fpm -- sh
# Inside pod:
php -r "
\$host = getenv('DB_HOST');
\$db = getenv('DB_NAME');
\$user = getenv('DB_USER');
\$pass = getenv('DB_PASSWORD');
try {
    \$pdo = new PDO(\"mysql:host=\$host;dbname=\$db\", \$user, \$pass);
    echo 'Connection successful\n';
} catch(PDOException \$e) {
    echo 'Connection failed: ' . \$e->getMessage() . '\n';
}
"
```

## Troubleshooting

### Pods Not Starting

1. Check pod status:
   ```bash
   kubectl describe pod <pod-name> -n mfg-dashboard
   ```

2. Common issues:
   - **ImagePullBackOff**: Image not found or registry access issue
   - **CrashLoopBackOff**: Application error, check logs
   - **Pending**: Resource constraints or node issues

### Database Connection Issues

1. Verify database is accessible from cluster
2. Check secret values:
   ```bash
   kubectl get secret mfg-dashboard-secret -n mfg-dashboard -o yaml
   ```
3. Test connectivity (see Database Connection Test above)

### Application Errors

1. Check PHP-FPM logs
2. Check Nginx error logs
3. Verify environment variables are set correctly
4. Check database schema is initialized

### Performance Issues

1. Check resource usage:
   ```bash
   kubectl top pods -n mfg-dashboard
   ```
2. Adjust resource limits in deployment.yaml
3. Scale up if needed
4. Check database performance

## Production Checklist

- [ ] Use specific image tags (not `latest`)
- [ ] Enable TLS/SSL in ingress
- [ ] Set appropriate resource limits
- [ ] Configure persistent storage for runtime/assets (if needed)
- [ ] Set up monitoring (Prometheus, Grafana)
- [ ] Configure centralized logging
- [ ] Set up database backups
- [ ] Configure network policies
- [ ] Enable HPA for auto-scaling
- [ ] Set up health checks and alerts
- [ ] Review security settings
- [ ] Test disaster recovery procedures

## Cleanup

To remove all resources:

```bash
# Delete all resources
kubectl delete -f .

# Or delete namespace (removes everything)
kubectl delete namespace mfg-dashboard
```

## Additional Resources

- [Kubernetes Documentation](https://kubernetes.io/docs/)
- [NGINX Ingress Controller](https://kubernetes.github.io/ingress-nginx/)
- [cert-manager](https://cert-manager.io/)
- Project documentation in `memory-bank/` directory

const STORAGE_BASE = "https://projects.isearchsolution.com/crm";

export function getImageUrl(path: string | null | undefined): string | null {
  if (!path) return null;
  if (path.startsWith("http")) return path;
  return `${STORAGE_BASE}${path}`;
}

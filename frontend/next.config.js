/** @type {import('next').NextConfig} */
const nextConfig = {
  env: {
    NEXT_PUBLIC_BACKEND_URL: process.env.NEXT_PUBLIC_BACKEND_URL
  },
  eslint: {
    ignoreDuringBuilds: true,
  },
}

module.exports = nextConfig

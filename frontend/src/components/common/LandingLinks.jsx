'use client'

import Link from 'next/link'
import { useAuth } from '@/hooks/auth'

const LandingLoginLinks = () => {
  const { user } = useAuth({ middleware: 'guest' })

  return (
    <p>
      {user ?
        <Link href="/dashboard" className="px-6 py-2 bg-blue-500 text-white rounded-full hover:bg-blue-600">Dashboard</Link> :
        <>
          <Link href="/login" className="px-6 py-2 bg-blue-500 text-white rounded-full hover:bg-blue-600">Login</Link>
          <Link href="/register" className="px-6 py-2 bg-red-600 text-white rounded-full hover:bg-red-500 ms-4">Register</Link>
        </>}
    </p>
  )
}

export default LandingLoginLinks

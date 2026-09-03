'use client'

import { useAuth } from '@/hooks/auth'
import React, { useEffect } from 'react'

const Logout = () => {
  const { logout } = useAuth({ middleware: 'auth' })

  useEffect(() => {
    logout()
  }, [])

  return (
    <div>Logging out...</div>
  )
}

export default Logout
'use client'

import Layout from '@/components/layout'
import { useAuth } from '@/hooks/auth'
import Loading from '@/app/(app)/Loading'
import customizeTheme from '@/theme/customizeTheme'
import { CssBaseline, ThemeProvider } from '@mui/material'
import { ToastContainer } from 'react-toastify'

const AppLayout = ({ children }) => {
  const { user, isLoading } = useAuth({ middleware: 'auth' })

  if (isLoading || !user) {
    return <Loading />
  }

  return (
    <ThemeProvider theme={customizeTheme}>
      <CssBaseline />
      <Layout>
        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
          {children}
          <ToastContainer
            position="top-right"
            autoClose={3000}
            hideProgressBar={false}
            newestOnTop={false}
            closeOnClick
            rtl={false}
            pauseOnFocusLoss
            draggable
            pauseOnHover
            theme="light"
          />
        </div>
      </Layout>
    </ThemeProvider>
  )
}

export default AppLayout

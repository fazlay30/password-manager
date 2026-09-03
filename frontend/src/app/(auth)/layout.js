import Link from 'next/link'
import AuthCard from '@/app/(auth)/AuthCard'

export const metadata = {
  title: 'HashLock',
}

const Layout = ({ children }) => {
  const logo = <Link href={'/'}>
    <img
      src={'/images/logo.jpg'}
      alt="logo"
      className='w-[180px] h-auto rounded-md'
    />
  </Link>

  return (
    <div>
      <div className="text-gray-900 antialiased">
        <AuthCard logo={logo}>
          {children}
        </AuthCard>
      </div>
    </div>
  )
}

export default Layout

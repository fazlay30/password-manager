const AuthCard = ({ logo, children }) => (
  <div className="min-h-screen flex flex-col lg:flex-row sm:justify-center gap-4 items-center pt-6 sm:pt-0 bg-gray-100">
    <div className="min-w-[340px]">
      {logo}
      <h2 className="font-bold text-4xl mt-4 mb-3">Your Password <br /> is Important</h2>
      <p>Use ILock to protect your password.</p>
    </div>

    <div className="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
      {children}
    </div>
  </div>
)

export default AuthCard

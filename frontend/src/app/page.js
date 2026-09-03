import LandingLoginLinks from "@/components/common/LandingLinks"

export const metadata = {
  title: 'HashLock',
}

const Home = () => {

  return (
    <>
      <section className="py-8 lg:py-20 bg-[linear-gradient(to_bottom,#c7ecff_0,#f9efff_100%)] min-h-screen relative
        before:content-[''] before:absolute before:left-0 before:top-0 before:h-full before:w-full before:bg-no-repeat before:bg-contain before:z-0">
        <div className="container max-w-6xl mx-auto relative z-10 px-2">
          <div className="grid lg:grid-cols-[1fr_1fr] items-center gap-5 min-h-[calc(100vh-160px)]">
            <div>
              <img src="/images/logo.jpg" alt="banner img" className='max-w-44 h-auto block lg:hidden' />
              <h2 className='font-bold lg:text-[52px] text-4xl lg:leading-[60px] leading-10 mb-4 mt-4'>Your Privacy <br /> is Important</h2>
              <p className='lg:text-xl text-base leading-7 mb-8'>
                ILock allows users to securely store passwords either individually or within groups. Users can create and manage groups, invite members, and share access to saved passwords within the group. All group members can view the shared passwords, ensuring seamless collaboration while maintaining security. 
                This approach provides both personal password management and a team-based solution for shared credentials.
              </p>
              <LandingLoginLinks />
            </div>
            <div className='lg:block hidden'>
              <div className="mb-4">
                <img src="/images/logo.jpg" alt="banner img" className='max-w-44 h-auto mx-auto' />
              </div>
              <img src="/images/hero-banner.jpg" alt="banner img" className='max-w-full h-auto rounded-full' />
            </div>
          </div>
        </div>
      </section>
    </>
  )
}

export default Home

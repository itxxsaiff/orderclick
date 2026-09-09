<section class="my-5 extras-margin">
    <div class="container">
        <div class="bg-light bg-change-mode p-4 rounded-4">
            <div class="col-12">
                <div class="d-flex gap-2 align-items-center">
                    <h3 class="page-title mb-0">
                        {{ trans('labels.have_question') }}
                    </h3>
                    <ul class="avatar-group d-none gap-1 d-md-flex justify-content-center flex-wrap m-0">
                        <li class="avatar_xs">
                            <img class="rounded-circle w-100 h-100"
                                src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQBvqzyx_zoi6q2c0Gd1XnE7wysD9PGOLe3-A&s"
                                alt="avatar">
                        </li>
                        <li class="avatar_xs">
                            <img class="rounded-circle w-100 h-100"
                                src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTExThKonFRjgL-5jVaa2XAGaOtm2dIZ0GVqw&s"
                                alt="avatar">
                        </li>
                        <li class="avatar_xs">
                            <img class="rounded-circle w-100 h-100"
                                src="https://media.gettyimages.com/id/944314810/photo/portrait-of-man.jpg?s=612x612&w=gi&k=20&c=ng0uGjz1MR1iafbOWQBpM_pKMANukpvmn7-pitQQ2hY="
                                alt="avatar">
                        </li>
                        <li class="avatar_xs">
                            <img class="rounded-circle w-100 h-100"
                                src="https://img.freepik.com/free-photo/portrait-young-businesswoman-holding-eyeglasses-hand-against-gray-backdrop_23-2148029483.jpg?size=338&ext=jpg&ga=GA1.1.2008272138.1725926400&semt=ais_hybrid"
                                alt="avatar">
                        </li>
                        <li class="avatar_xs">
                            <img class="rounded-circle w-100 h-100"
                                src="https://img.freepik.com/free-photo/waist-up-portrait-handsome-serious-unshaven-male-keeps-hands-together-dressed-dark-blue-shirt-has-talk-with-interlocutor-stands-against-white-wall-self-confident-man-freelancer_273609-16320.jpg?size=626&ext=jpg&ga=GA1.1.1141335507.1719187200&semt=ais_user"
                                alt="avatar">
                        </li>
                    </ul>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 align-items-center justify-content-between">
                    <div class="col-xl-10 col-md-9">
                        <p class="m-0 fs-7 text-muted line-2">
                            {{ trans('labels.have_question_note') }}
                        </p>
                    </div>
                    <div class="col-sm-auto col-12">
                        <a href="{{ URL::to(@$storeinfo->slug . '/contact') }}"
                            class="btn btn-secondary w-100 rounded px-sm-4 px-3 py-2 fs-15 fw-500">
                            {{ trans('labels.contact_us') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>